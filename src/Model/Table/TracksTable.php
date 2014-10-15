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

    public function searchCriteria($criteria, $limit = 10)
    {
        return $this->find()
            ->where(["title LIKE" => sprintf("%%%s%%", $criteria)])
            ->limit($limit)
            ->order("LOCATE('".$criteria."', title)", "ASC")
            ->order("title", "ASC")
            ->contain(['LastfmTracks', 'Albums' => ['Artists'], 'TrackReviewSnapshots']);
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

}
