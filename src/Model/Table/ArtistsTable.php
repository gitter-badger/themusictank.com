<?php
namespace App\Model\Table;

use App\Model\Api\LastfmApi;
use App\Model\Entity\Artist;

use Cake\I18n\Time;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\ORM\Query;

class ArtistsTable extends Table {

    public function initialize(array $config) {

        $this->hasOne('LastfmArtists',          ['propertyName' => 'lastfm', 'dependent' => true]);
        $this->hasOne('ArtistReviewSnapshots',  ['propertyName' => 'snapshot', 'dependent' => true]);
        $this->hasMany('Albums');

        $this->addBehavior('Sluggable', ['contain' => [
            'LastfmArtists',
            'Albums' => ['AlbumReviewSnapshots'],
            'ArtistReviewSnapshots'
        ]]);
        $this->addBehavior('Syncable');
        $this->addBehavior('Thumbnail');
    }

    public function findLocalOrRemote($query)
    {
        $query      = trim($query);
        $resultSet  = $this->searchCriteria($query, 3)->all();

        // When no matches are found in the table, query Lastfm
        // for results.
        if (!count($resultSet)) {
            $resultSet = [];
            $lastfmApi = new LastfmApi();
            foreach ($lastfmApi->searchArtists($query, 10) as $value) {
                $artist = new Artist();
                $artist->loadFromLastFm($value);
                $this->save($artist);
                $resultSet[] = $artist;
            }
        }

        return $resultSet;
    }

    public function syncToRemote(Artist $artist)
    {
        $lastfmApi = new LastfmApi();
        $artist->loadFromLastFm($lastfmApi->getArtistInfo($artist));

        // This is not normal...
        TableRegistry::get('LastfmArtists')->touch($artist->lastfm, 'Lastfm.onUpdate');
        TableRegistry::get('LastfmArtists')->save($artist->lastfm);

        return $this->save($artist);
    }

    public function findExpired(Query $query, array $options = [])
    {
        $options += [
            'timeout' => 0,
            'limit' => 200,
            'contain' => ['LastfmArtists']
        ];

        return $query
            ->contain($options['contain'])
            ->where(['LastfmArtists.modified < ' => (int)$options['timeout']])
            ->orWhere(['LastfmArtists.modified IS NULL'])
            ->limit((int)$options['limit']);
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


    public function getWithExpiredDiscographies($timeout = 0, $limit = 200)
    {
        return $this->find()
            ->contain(['Albums'])
            ->where(['Artists.modified < ' => $timeout])
            ->orWhere(['Artists.modified IS NULL'])
            ->limit($limit);
    }

    /**
     * When saving a batch of entities, ensure the model doesn't already exist before
     * sending the data
     */
    public function saveLastFmBatch($artists)
    {
        // The only unique key we have is the mbid sent in by Last.fm. Collect it
        // and test against this to check for possible duplicates.
        $mbids = [];
        foreach ($artists as $artist) {
            if (trim($artist->lastfm->mbid) != "") {
                $mbids[] = $artist->lastfm->mbid;
            }
        }

        // Get existing records
        $existing = $this->find()
            ->select(['id', 'LastfmArtists.id', 'LastfmArtists.artist_id', 'LastfmArtists.mbid'])
            ->contain(['LastfmArtists'])
            ->where(['LastfmArtists.mbid IN' => array_unique($mbids)])
            ->limit(count($artists))
            ->all();

        if (count($existing)) {
            // Create an indexed pointer map to associate more easily
            $map = [];
            foreach($existing as $record) {
                $map[$record->lastfm->mbid] = $record;
            }

            // Preserve association of existing data over lastfm's information.
            foreach ($artists as $artist) {
                if(array_key_exists($artist->lastfm->mbid, $map)) {
                    $entity = $map[$artist->lastfm->mbid];
                    $artist->id = $entity->id;
                    $artist->lastfm->id = $entity->lastfm->id;
                    $artist->lastfm->artist_id = $entity->lastfm->artist_id;
                }
            }
        }

        // Finally, save the whole batch.
        foreach ($artists as $artist) {
            // @todo : This prevents Jay-Z from appearing. I should consider another filter.
            if (trim($artist->lastfm->mbid) != "") {
                $this->save($artist);
            }
        }
    }

}
