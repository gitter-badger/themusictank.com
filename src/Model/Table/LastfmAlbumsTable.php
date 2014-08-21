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

}
