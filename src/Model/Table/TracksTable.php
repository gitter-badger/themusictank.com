<?php
namespace App\Model\Table;

use App\Model\Api\LastfmApi;
use App\Model\Entity\Track;
use App\Model\Entity\Album;

use Cake\I18n\Time;
use Cake\ORM\Table;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\Utility\Hash;

class TracksTable extends Table {

    public function initialize(array $config)
    {
        $this->belongsTo('Albums');

        $this->hasOne('LastfmTracks', ['propertyName' => 'lastfm']);
        $this->hasOne('TrackReviewSnapshots', ['propertyName' => 'snapshot']);
        $this->hasOne('TrackYoutubes', ['propertyName' => 'youtube']);

        $this->addBehavior('Sluggable', ['contain' => [
            'Artists' => ['LastfmArtists'],
            'LastfmAlbums',
            'AlbumReviewSnapshots',
            'Tracks' => ['TrackYoutubes', 'TrackReviewSnapshots']
        ]]);
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

    public function getByAlbumIds($albumIds)
    {
        return $this->find()
            ->where(["album_id IN" => $albumIds])
            ->contain(['TrackReviewSnapshots']);
    }

    public function getByAlbumId($albumId)
    {
        return $this->find()
            ->where(["album_id" => $albumId])
            ->contain(['TrackReviewSnapshots']);
    }

    public function findNewDailyChallenger()
    {
        $nbResults = $this->find()->count();
        $newFeatured = $this->find()
            ->where(['is_challenge' => false])
            ->offset(rand(0, $nbResults))
            ->limit(1)->first();

        if ($newFeatured) {
            $this->query()->update()
                ->set(['is_challenge' => false])
                ->where(['is_challenge' => true])
                ->execute();

            $newFeatured->is_challenge = true;
            $this->save($newFeatured);

            return $newFeatured;
        }
    }

    public function findSearch(Query $query, array $options = [])
    {
        $options += [
            'limit' => 10,
            'criteria' => null,
            'contain' => ['LastfmTracks', 'Albums' => ['Artists'], 'TrackReviewSnapshots']
        ];

        if(is_null($options['criteria'])) {
            throw new Exception("Missing 'criteria' query parameter.");
        }

        return $query
            ->where(["title LIKE" => sprintf("%%%s%%", $options['criteria'])])
            ->limit((int)$options['limit'])
            ->order(sprintf("LOCATE('%s', title)", $options['criteria']), "ASC")
            ->order("title", "ASC")
            ->contain($options['contain']);
    }

    public function getOEmbedDataBySlug($slug)
    {
        return $this->find()
            ->select([
                'Tracks.title', 'Tracks.slug',
                'TrackReviewSnapshots.total', 'TrackReviewSnapshots.liking', 'TrackReviewSnapshots.liking_pct',
                'TrackReviewSnapshots.disliking', 'TrackReviewSnapshots.disliking_pct','TrackReviewSnapshots.neutral', 'TrackReviewSnapshots.neutral_pct',
                'TrackReviewSnapshots.disliking', 'TrackReviewSnapshots.disliking_pct','TrackReviewSnapshots.neutral', 'TrackReviewSnapshots.neutral_pct',
                'TrackReviewSnapshots.curve', 'TrackReviewSnapshots.ranges','TrackReviewSnapshots.score', 'TrackReviewSnapshots.top', 'TrackReviewSnapshots.bottom'
            ])
            ->where(["slug" => $slug])
            ->contain(['TrackReviewSnapshots']);
    }


    /**
     * Fetches the tracks having the same mbid or no mbids and the exact name.
     */
    public function findMatchingKeys(Query $query, array $options = [])
    {
        $options += [
            'keys' => null,
            'contain' => ['LastfmTracks']
        ];

        if (is_null($options['keys'])) {
            throw new Exception("Missing 'values' parameter.");
        }

        return $query
            ->where(['LastfmTracks.mbid IN' => $options['keys']])
            ->orWhere(['title IN' => $options['keys'], 'LastfmTracks.mbid' => null, 'album_id' => $options['album']->id])
            ->limit(count($options['keys']))
            ->contain($options['contain']);
    }

    /**
     * Takes an existing Artist and updates its values with what is returned by the Lastfm API.
     */
    public function syncToRemote(Track $track)
    {
        $lastfmApi = new LastfmApi();
        $track->loadFromLastFm($lastfmApi->getTrackInfo($track));

        TableRegistry::get('LastfmTracks')->touch($track->lastfm, 'Lastfm.onUpdate');
        // @todo : This is not normal, saving should cascade...
        TableRegistry::get('LastfmTracks')->save($track->lastfm);
        return $this->save($track);
    }

    public function findExpired(Query $query, array $options = [])
    {
        $options += [
            'timeout' => new Time(),
            'limit' => 200,
            'contain' => ['LastfmTracks', 'Albums' => ['Artists']]
        ];

        return $query
            ->contain($options['contain'])
            ->where(['LastfmTracks.modified < ' => $options['timeout']])
            ->orWhere(['LastfmTracks.modified IS NULL'])
            ->limit((int)$options['limit']);
    }

    /**
     * When saving a batch of entities, ensure the model doesn't already exist before
     * sending the data. Unlike other entities, this function does not actually save.
     */
    public function assignLastFmBatch(Album $album, array $trackData)
    {
        $map = $this->_createLastFmMap($trackData);
        $trackList = $this->find('matchingKeys', [ 'keys' => array_keys($map), 'album' => $album ])->toArray();

        // Update/compare existing records
        foreach ($trackList as $track) {
            $data = null;
            if (!is_null($track->lastfm->mbid)) {
                $data = $map[$track->lastfm->mbid];
                unset($map[$track->lastfm->mbid]);
            } else {
                $data = $map[$track->name];
                unset($map[$track->name]);
            }

            if (!is_null($data)) {
                $track->compareData($data);
            }
        }

        // Create the new ones from the left-overs
        foreach ($map as $values) {
            $track = new Track();
            $track->loadFromLastFm($values);
            $trackList[] = $track;
        }

        $album->tracks = $trackList;

        return $album;
    }

    /**
     *  Creates a pointer map of the data sent to sort the results
     *  more quickly.
     */
    protected function _createLastFmMap(array $albumData)
    {
        $map = [];
        $trackData = Hash::extract($albumData, 'tracks.track.{n}');

        foreach ($trackData as $track) {
            // Check valid key values for unique matches.
            foreach (['mbid', 'name'] as $property) {
                $uniqueKeyValue = trim($track[$property]);
                if (!empty($uniqueKeyValue)) {
                    $map[$uniqueKeyValue] = $track;
                    break;
                }
            }
        }

        return $map;
    }

}
