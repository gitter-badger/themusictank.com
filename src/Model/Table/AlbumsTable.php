<?php
namespace App\Model\Table;

use App\Model\Entity\Artist;
use App\Model\Api\LastfmApi;
use App\Model\Entity\Album;

use Cake\I18n\Time;
use Cake\ORM\Table;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

use Exception;

class AlbumsTable extends Table {

    public function initialize(array $config)
    {
        $this->belongsTo('Artists');

        $this->hasOne('LastfmAlbums', ['propertyName' => 'lastfm']);
        $this->hasOne('AlbumReviewSnapshots', ['propertyName' => 'snapshot']);
        $this->hasMany('Tracks');

        $this->addBehavior('Sluggable', ['contain' => [
            'Artists' => ['LastfmArtists'],
            'LastfmAlbums',
            'AlbumReviewSnapshots',
            'Tracks' => ['TrackYoutubes', 'TrackReviewSnapshots']
        ]]);
        $this->addBehavior('Syncable');
        $this->addBehavior('Thumbnail');
    }


    public function findExpired(Query $query, array $options = [])
    {
        $options += [
            'timeout' => 0,
            'limit' => 200,
            'contain' => ['LastfmAlbums', 'Artists', 'Tracks']
        ];

        return $query
            ->where(['LastfmAlbums.modified < ' => $options['timeout']])
            ->orWhere(['LastfmAlbums.modified IS NULL'])
            ->contain($options['contain'])
            ->limit((int)$options['limit']);
    }

    public function findMissingSnapshots(Query $query, array $options = [])
    {
        return $query
            ->select(['id','name'])
            ->where(['id IN' => TableRegistry::get("AlbumReviewSnapshots")->getIdsWithNoSnapshots()]);
    }

    public function findUniqueSlug(Query $query, array $options = [])
    {
        // I bet this could be improved. For now, loop until we have a unique slug
        // in the model's table.
        $i = 0;
        $slug = strtolower(Inflector::slug($options['slug']));

        while ($this->findBySlug($slug)->count() > 0) {
            if (!preg_match ('/-{1}[0-9]+$/', $slug )) {
                $slug .= '-' . ++$i;
            }
            else {
                $slug = preg_replace ('/[0-9]+$/', ++$i, $slug );
            }
        }

        return $slug;
    }


    public function findListArtistIds(Query $query, array $options = [])
    {
        return [-1] + $query->select(['Albums.artist_id', 'LastfmAlbums.id'])->distinct(['Albums.artist_id'])->contain(['LastfmAlbums'])->extract('Albums.artist_id')->toArray();
    }


    /**
     * Fetches the artists having the same mbid or no mbids and the exact name.
     */
    public function findMatchingKeys(Query $query, array $options = [])
    {
        $options += [
            'keys' => null,
            'contain' => ['LastfmAlbums']
        ];

        if (is_null($options['keys'])) {
            throw new Exception("Missing 'values' parameter.");
        }

        return $query
            ->where(['LastfmAlbums.mbid IN' => $options['keys']])
            ->orWhere(['name IN' => $options['keys'], 'LastfmAlbums.mbid' => null])
            ->limit(count($options['keys']))
            ->contain($options['contain']);
    }

    /** Fetches only the album names. The details will have to be pulled at another time.
     *  This is due to limitations in Lastfm's api data.
     */
    public function findUpdatedDiscography(Query $query, array $options = [])
    {
        $options += [
            'artist' => null
        ];

        if(is_null($options['artist'])) {
            throw new Exception("Missing required 'artist' parameter.");
        }

        $artist = $options['artist'];
        if ($artist->requiresUpdate()) {

            $shell = null;
            if (array_key_exists("shell", $options)) {
                $shell = $options['shell'];
                $shell->out("\t\t\tQuerying LastFm...");
            }

            $lastfmApi = new LastfmApi();
            $this->saveLastFmBatch($lastfmApi->getArtistTopAlbums($artist), $artist, $shell);

            // Save update timestamp on artist.
            $tblArtists = TableRegistry::get('LastfmArtists');
            $artist->set("modified_discography", new Time());
            $tblArtists->save($artist);
        }
    }


    /**
     * Takes an existing Artist and updates its values with what is returned by the Lastfm API.
     */
    public function syncToRemote(Album $album)
    {
        $lastfmApi = new LastfmApi();
        $infos = $lastfmApi->getAlbumInfo($album);
        $album->loadFromLastFm($infos);

        // Preload/modify the tracks too.
        TableRegistry::get('Tracks')->assignLastFmBatch($album, $infos);

        // @todo : This is not normal, saving should cascade...
        $tblLastfm = TableRegistry::get('LastfmAlbums');
        $tblLastfm->touch($album->lastfm, 'Lastfm.onUpdate');
        $tblLastfm->save($album->lastfm);

        return $this->save($album);
    }


    /**
     * When saving a batch of entities, ensure the model doesn't already exist before
     * sending the data
     */
    public function saveLastFmBatch(array $albumData, Artist $artist, $shell = null)
    {
        $map = $this->_createLastFmMap($albumData);
        $albumList = $this->find('matchingKeys', [ 'keys' => array_keys($map) ])->toArray();

        // Update/compare existing records
        foreach ($albumList as $album) {
            $data = null;
            if (!is_null($album->lastfm->mbid)) {
                $data = $map[$album->lastfm->mbid];
                unset($map[$album->lastfm->mbid]);
            } else {
                $data = $map[$album->name];
                unset($map[$album->name]);
            }

            if (!is_null($data)) {
                $album->compareData($data);
            }
        }

        // Create the new ones from the left-overs
        foreach ($map as $values) {
            $album = new Album();
            $album->loadFromLastFm($values);
            $album->artist_id = $artist->id;
            $albumList[] = $album;
        }

        // Finally, save the whole batch.
        foreach ($albumList as $album) {
            if (!is_null($shell)) {
                $shell->out("\t\t\tUpdating <info>" . $album->name ."</info>...");
            }
            $this->save($album);
        }


        return $albumList;
    }

    /**
     *  Creates a pointer map of the data sent to sort the results
     *  more quickly.
     */
    protected function _createLastFmMap(array $albumData)
    {
        $map = [];

        foreach ($albumData as $album) {
            // Check valid key values for unique matches.
            foreach (['mbid', 'name'] as $property) {
                $uniqueKeyValue = trim($album[$property]);
                if (!empty($uniqueKeyValue)) {
                    $map[$uniqueKeyValue] = $album;
                    break;
                }
            }
        }

        return $map;
    }

    public function findNewReleases(Query $query, array $options = [])
    {
        $options += [
            'limit' => 1,
            'contain' => ['Artists', 'LastfmAlbums', 'AlbumReviewSnapshots', 'Tracks']
        ];

        return $query->order('release_date', 'DESC')->contain($options['contain']);
    }

    public function getByArtistId($artistId)
    {
        return $this->find()
            ->where(["artist_id" => $artistId])
            ->contain(['AlbumReviewSnapshots']);
    }

    public function getIdsByArtist($artistId)
    {
        return $this->find()
            ->select('id')
            ->where(["artist_id" => $artistId]);
    }

    public function findSearch(Query $query, array $options = [])
    {
        $options += [
            'limit' => 10,
            'criteria' => null,
            'contain' => ['AlbumReviewSnapshots', 'Artists']
        ];

        if(is_null($options['criteria'])) {
            throw new Exception("Missing 'criteria' query parameter.");
        }

        return $query
            ->where(["name LIKE" => sprintf("%%%s%%", $options['criteria'])])
            ->limit((int)$options['limit'])
            ->order(sprintf("LOCATE('%s', name)", $options['criteria']), "ASC")
            ->order("name", "ASC")
            ->contain($options['contain']);
    }

    public function getOEmbedDataBySlug($slug)
    {
        return $this->find()
            ->select([
                'Albums.name', 'Albums.slug',
                'AlbumReviewSnapshots.total', 'AlbumReviewSnapshots.liking', 'AlbumReviewSnapshots.liking_pct',
                'AlbumReviewSnapshots.disliking', 'AlbumReviewSnapshots.disliking_pct','AlbumReviewSnapshots.neutral', 'AlbumReviewSnapshots.neutral_pct',
                'AlbumReviewSnapshots.disliking', 'AlbumReviewSnapshots.disliking_pct','AlbumReviewSnapshots.neutral', 'AlbumReviewSnapshots.neutral_pct',
                'AlbumReviewSnapshots.curve', 'AlbumReviewSnapshots.ranges','AlbumReviewSnapshots.score', 'AlbumReviewSnapshots.top', 'AlbumReviewSnapshots.bottom'
            ])
            ->where(["slug" => $slug])
            ->contain(['AlbumReviewSnapshots']);
    }
}
