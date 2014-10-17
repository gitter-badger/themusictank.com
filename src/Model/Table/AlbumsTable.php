<?php
namespace App\Model\Table;

use App\Model\Entity\Artist;
use App\Model\Api\LastfmApi;

use Cake\ORM\Table;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

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
            $lastfmApi = new LastfmApi();
            return $this->saveLastFmBatch($lastfmApi->getArtistTopAlbums($artist));
        }
    }

    /**
     * When saving a batch of entities, ensure the model doesn't already exist before
     * sending the data
     */
    public function saveLastFmBatch(array $albumData)
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
            TableRegistry::get('Tracks')->saveLastFmBatch($values['tracks'], $album)


            $albumList[] = $album;
        }

        // Finally, save the whole batch.
        foreach ($albumList as $album) {
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
