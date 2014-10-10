<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use App\Model\Entity\Artist;
use App\Model\Entity\Album;
use App\Model\Entity\LastfmAlbum;

class LastfmAlbumsTable extends Table
{

    public function initialize(array $config)
    {
        $this->belongsTo('Albums');
    }


    public function getMbidsByArtist(Artist $artist)
    {
        return $this->find()
            ->select(["mbid"])
            ->where(["Albums.artist_id" => $artist->id])
            ->contain(['Albums']);
    }

    /**
     *  This is used only when creating empty album shell with no details other than the title (ex: from ajax search)
     *  return bool True if there was additions
     */
    public function filterNewByArtist(Artist $artist, array $apiAlbums)
    {
        $currentMbids = $this->getMbidsByArtist($artist)->extract("mbid")->toArray();
        foreach($apiAlbums as $apiAlbum) {
            if(trim($apiAlbum['mbid']) != "" && !in_array($apiAlbum['mbid'], $currentMbids)) {
                $album = new Album();
                $album->name = $apiAlbum['name'];
                $album->lastfm = new LastfmAlbum();
                $album->lastfm->mbid = $album->mbid;
                $artist->albums[] = $album;
            }
        }

        return count($artist->albums) > count($currentMbids);
    }


    /**
     * When saving a batch of entities, ensure the model doesn't already exist before
     * sending the data
     */
    public function saveLastFmBatch($albums)
    {
        // The only unique key we have is the mbid sent in by Last.fm. Collect it
        // and test against this to check for possible duplicates.
        $mbids = [];
        foreach ($albums as $album) {
            if (trim($album->lastfm->mbid) != "") {
                $mbids[] = $album->lastfm->mbid;
            }
        }

        // Get existing records
        $existing = $this->find()
            ->select(['id', 'LastfmAlbums.id', 'LastfmAlbums.album_id', 'LastfmAlbums.mbid'])
            ->contain(['LastfmAlbums'])
            ->where(['LastfmAlbums.mbid IN' => array_unique($mbids)])
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
