<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class ArtistsTable extends Table {

    public function initialize(array $config) {
        $this->hasOne('LastfmArtists', ['className' => 'LastfmArtists', 'propertyName' => 'lastfm']);
        $this->hasOne('ArtistReviewSnapshots', ['className' => 'ArtistReviewSnapshots', 'propertyName' => 'snapshot']);

        $this->hasMany('Albums');
    }

    /**
     */
    public function getBySlug($slug)
    {
        return $this->find()
            ->where(['slug' => $slug])
            ->contain(['LastfmArtists', 'Albums' => ['AlbumReviewSnapshots'], 'ArtistReviewSnapshots']);
    }

    /**
     * Finds all artists that have been flagged as popular.
     * @param int $limit Number of records to fetch (default 1)
     * @return array Dataset of popular Artists.
     */
    public function findPopular($limit = 1)
    {
        return $this->find()
            ->where(['LastfmArtists.is_popular' => true])
            ->order(['rand()'])
            ->limit((int)$limit)
            ->contain(['LastfmArtists', 'Albums', 'ArtistReviewSnapshots']);
    }

    /**
     * Fetches a list of possible categories based on the name of all our artists
     * @return array An array of capitalized letters
     */
    public function getAvaillableFirstLetters()
    {
        return $this->find()
            ->select(['letter' => 'UCASE(LEFT(name, 1))'])
            ->distinct(['letter'])
            ->order(['letter' => 'ASC'])
            ->extract("letter");
    }

    public function searchCriteria($criteria, $limit = 10)
    {
        return $this->find()
            ->where(["name LIKE" => sprintf("%%%s%%", $criteria)])
            ->limit($limit)
            ->order("LOCATE('".$criteria."', name)", "ASC")
            ->order("name", "ASC")
            ->contain(['LastfmArtists', 'Albums' => ['AlbumReviewSnapshots'], 'ArtistReviewSnapshots']);
    }

    public function browse($letter, $limit = 10)
    {
        return $this->find()
            ->where(["name LIKE" => sprintf("%s%%", $letter)])
            ->limit($limit)
            ->order("name", "ASC")
            ->contain(['LastfmArtists', 'Albums' => ['AlbumReviewSnapshots'], 'ArtistReviewSnapshots']);
    }

    public function getOEmbedDataBySlug($slug)
    {
        return $this->find()
            ->select([
                'Artists.name', 'Artists.slug',
                'ArtistReviewSnapshots.total', 'ArtistReviewSnapshots.liking', 'ArtistReviewSnapshots.liking_pct',
                'ArtistReviewSnapshots.disliking', 'ArtistReviewSnapshots.disliking_pct','ArtistReviewSnapshots.neutral', 'ArtistReviewSnapshots.neutral_pct',
                'ArtistReviewSnapshots.disliking', 'ArtistReviewSnapshots.disliking_pct','ArtistReviewSnapshots.neutral', 'ArtistReviewSnapshots.neutral_pct',
                'ArtistReviewSnapshots.curve', 'ArtistReviewSnapshots.ranges','ArtistReviewSnapshots.score', 'ArtistReviewSnapshots.top', 'ArtistReviewSnapshots.bottom'
            ])
            ->where(["slug" => $slug])
            ->contain(['ArtistReviewSnapshots']);
    }

}
