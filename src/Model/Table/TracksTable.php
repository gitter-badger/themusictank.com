<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class TracksTable extends Table {

    public function initialize(array $config)
    {
        $this->belongsTo('Albums');

        $this->hasOne('LastfmTracks', ['propertyName' => 'lastfm']);
        $this->hasOne('TrackReviewSnapshots', ['propertyName' => 'snapshot']);
        $this->hasOne('TrackYoutubes', ['propertyName' => 'youtube']);
    }

    public function getBySlug($slug)
    {
        return $this->find()
            ->where(['Tracks.slug' => $slug])
            ->contain(['Albums' => ['Artists' => ['LastfmArtists']], 'LastfmTracks', 'TrackReviewSnapshots', 'TrackYoutubes']);
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
        $newFeatured = $this->find()
            ->where(['is_challenge' => false])
            ->order(['rand()'])
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
            ->orWhere(['title IN' => $options['keys'], 'LastfmTracks.mbid' => null])
            ->limit(count($options['keys']))
            ->contain($options['contain']);
    }

    /**
     * When saving a batch of entities, ensure the model doesn't already exist before
     * sending the data
     */
    public function saveLastFmBatch(array $trackData)
    {
        $map = $this->_createLastFmMap($trackData);
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
    protected function _createLastFmMap(array $trackData)
    {
        $map = [];

        foreach ($trackData as $track) {
            // Check valid key values for unique matches.
            foreach (['mbid', 'title'] as $property) {
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
