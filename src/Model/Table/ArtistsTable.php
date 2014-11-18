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
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

use Exception;

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
        $this->addBehavior('Thumbnail');
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

    private function _createFromLastFm(array $data)
    {
        $artist = new Artist();
        $artist->loadFromLastFm($data);
        return $artist;
    }

    public function findLocalOrRemote(Query $query, array $options = [])
    {
        $resultSet  = $this->find('search', $options)->all();
        if (count($resultSet)) {
            return $resultSet;
        }

        // When no matches are found in the table, query Lastfm
        // for results.
        return $this->find('remote', $options);
    }

    /**
     *
     */
    public function findRemote(Query $query, array $options = [])
    {
        if(is_null($options['criteria'])) {
            throw new Exception("Missing 'criteria' query parameter.");
        }

        $lastfmApi = new LastfmApi();
        $remoteResults = $lastfmApi->searchArtists($options['criteria'], 10);
        $resultSet = [];

        if (count($remoteResults)) {
            // Single results do no return a loopable array.
            if (array_key_exists("name", $remoteResults)) {
                $resultSet[] = $this->_createFromLastFm($remoteResults);
            } else {
                foreach ($remoteResults as $value) {
                    $resultSet[] = $this->_createFromLastFm($value);
                }
            }
        }

        $this->saveLastFmBatch($resultSet);
        return $resultSet;
    }

    /**
     * Takes an existing Artist and updates its values with what is returned by the Lastfm API.
     */
    public function syncToRemote(Artist $artist)
    {
        $lastfmApi = new LastfmApi();
        $artistInfo = $lastfmApi->getArtistInfo($artist);
        $artist->compareData($artistInfo);

        TableRegistry::get('LastfmArtists')->touch($artist->lastfm, 'Lastfm.onUpdate');
        // @todo : This is not normal, saving should cascade...
        TableRegistry::get('LastfmArtists')->save($artist->lastfm);

        return $this->save($artist);
    }

    public function findExpired(Query $query, array $options = [])
    {
        $options += [
            'timeout' => new Time(),
            'limit' => 200,
            'contain' => ['LastfmArtists', 'Albums' => ['Artists']]
        ];

        return $query
            ->contain($options['contain'])
            ->where(['LastfmArtists.modified < ' => $options['timeout']->toUnixString()])
            ->orWhere(['LastfmArtists.modified IS NULL'])
            ->limit((int)$options['limit']);
    }

    public function findExpiredDiscographies(Query $query, array $options = [])
    {
        $options += [
            'timeout' => new Time(),
            'limit' => 200,
            'contain' => ['LastfmArtists', 'Albums' => ['LastfmAlbums']]
        ];

        return $this->find()
            ->contain($options['contain'])
            ->limit((int)$options['limit'])
            ->where(['LastfmArtists.modified_discography < ' => $options['timeout']->toUnixString()])
            ->orWhere(['LastfmArtists.modified_discography IS NULL']);
    }

    public function findMissingDiscographies(Query $query, array $options = [])
    {
        $options += [
            'timeout' => 0,
            'limit' => 200,
            'contain' => ['Albums' => ['LastfmAlbums']]
        ];

        return $this->find()
            ->contain($options['contain'])
            ->where(['id NOT IN ' => TableRegistry::get('Albums')->find('listArtistIds')])
            ->limit((int)$options['limit']);
    }

    public function findListIds(Query $query, array $options = [])
    {
        return [-1] + $query->select(['id'])->extract('id')->toArray();
    }

    /**
     * Finds all artists that have been flagged as popular.
     * @return array Dataset of popular Artists.
     */
    public function findPopular(Query $query, array $options = [])
    {
        $nbResults = $this->find()->count();
        $options += [
            'limit' => 1,
            'offset' => rand(0, $nbResults),
            'contain' => ['LastfmArtists', 'Albums', 'ArtistReviewSnapshots']
        ];

        return $query
            ->contain($options['contain'])
            ->where(['LastfmArtists.is_popular' => true])
            ->offset($options['offset'])
            ->limit((int)$options['limit']);
    }

    /**
     * Fetches a list of possible categories based on the name of all our artists
     * @TOTO : Validate/remove/merge numbers and signs
     * @return array An array of capitalized letters
     */
    public function findFirstLetters(Query $query, array $options = [])
    {
        return $query
            ->select(['letter' => 'SUBSTR(name, 0, 1)'])
            ->distinct(['letter'])
            ->order('letter', 'ASC')
            ->extract("letter");
    }

    public function findSearch(Query $query, array $options = [])
    {
        $options += [
            'limit' => 10,
            'criteria' => null,
            'contain' => ['LastfmArtists', 'Albums' => ['AlbumReviewSnapshots'], 'ArtistReviewSnapshots']
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

    public function findBrowse(Query $query, array $options = [])
    {
        $options += [
            'limit' => 10,
            'letter' => null,
            'contain' => ['LastfmArtists', 'Albums' => ['AlbumReviewSnapshots'], 'ArtistReviewSnapshots']
        ];

        if(is_null($options['letter'])) {
            throw new Exception("Missing 'letter' query parameter.");
        }

        return $query
            ->where(["name LIKE" => sprintf("%s%%", $options['letter'])])
            ->limit((int)$options['limit'])
            ->order("name", "ASC")
            ->contain($options['contain']);
    }

    /**
     * Fetches the artists having the same mbid or no mbids and the exact name.
     */
    public function findMatchingKeys(Query $query, array $options = [])
    {
        $options += [
            'keys' => null,
            'contain' => ['LastfmArtists']
        ];

        if (is_null($options['keys'])) {
            throw new Exception("Missing 'values' parameter.");
        }

        return $query
            ->where(['LastfmArtists.mbid IN' => $options['keys']])
            ->orWhere(['name IN' => $options['keys']])
            ->limit(count($options['keys']))
            ->contain($options['contain']);
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

    /**
     * When saving a batch of entities, ensure the model doesn't already exist before
     * sending the data
     */
    public function saveLastFmBatch(array $artistData)
    {
        $map = $this->_createLastFmMap($artistData);
        $artistList = $this->find('matchingKeys', [ 'keys' => array_keys($map) ])->toArray();

        // Update/compare existing records
        foreach ($artistList as $artist) {
            $data = null;
            if (!is_null($artist->lastfm->mbid)) {
                $data = $map[$artist->lastfm->mbid];
                unset($map[$artist->lastfm->mbid]);
            } else {
                $data = $map[$artist->name];
                unset($map[$artist->name]);
            }

            if (!is_null($data)) {
                $artist->compareData($data);
            }
        }

        // Create the new ones from the left-overs
        foreach ($map as $values) {
            $artist = new Artist();
            $artist->loadFromLastFm($values);
            $artistList[] = $artist;
        }

        // Finally, save the whole batch.
        foreach ($artistList as $artist) {
            $this->save($artist);
        }

        return $artistList;
    }

    /**
     *  Creates a pointer map of the data sent to sort the results
     *  more quickly.
     */
    protected function _createLastFmMap(array $artistData)
    {
        $map = [];

        foreach ($artistData as $artist) {
            // Check valid key values for unique matches.
            foreach (['mbid', 'name'] as $property) {
                if(array_key_exists($property, $artist)) {
                    $uniqueKeyValue = trim($artist[$property]);
                    if (!empty($uniqueKeyValue)) {
                        $map[$uniqueKeyValue] = $artist;
                        break;
                    }
                }
            }
        }

        return $map;
    }

}
