<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query;
use App\Model\Entity\Artist;
use App\Model\Api\LastfmApi;
use Cake\ORM\TableRegistry;

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

    /** Fetches only the album names. The details will have to be pulled at another time.
     *  This is due to limitations in Lastfm's api data.
     */
    public function fetchDiscography(Artist $artist)
    {
        if ($artist->requiresUpdate()) {

            $lastfmApi = new LastfmApi();
            $topAlbums = $lastfmApi->getArtistTopAlbums($artist);
            if ($this->updateArtistAlbums($artist, $topAlbums)) {
                foreach ($artist->albums as $album) {
                    // Because the fetchDiscography function is all
                    // about getting album names, only save new albums.
                    if($album->isNew()) {
                        $this->save($album);
                    }
                }
            }

            // Update modified time on artist.
            $tblArtists = TableRegistry::get('Artists');
            $tblArtists->touch($artist, 'Lastfm.onUpdate');
            return $tblArtists->save($artist);
        }

        return false;
    }

    /**
     *  This is used only when creating empty album shell with no details other than the title (ex: from ajax search)
     *  return bool True if there was additions
     */
    public function updateArtistAlbums(Artist $artist, array $apiAlbums)
    {
        $currentMbids = TableRegistry::get('LastfmAlbums')->find('listMbids', ['artist' => $artist]);
        foreach($apiAlbums as $apiAlbum) {
            if(trim($apiAlbum['mbid']) != "" && !in_array($apiAlbum['mbid'], $currentMbids)) {
                $artist->albums[] = $this->newEntity([
                    'name' => $apiAlbum['name'],
                    'artist_id' => $artist->id,
                    'lastfm' => [
                        'mbid' => $apiAlbum['mbid']
                    ]
                ]);;
            }
        }

        return count($artist->albums) > count($currentMbids);
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

    public function searchCriteria($criteria, $limit = 10)
    {
        return $this->find()
            ->where(["Albums.name LIKE" => sprintf("%%%s%%", $criteria)])
            ->limit($limit)
            ->order("LOCATE('".$criteria."', Albums.name)", "ASC")
            ->order("Albums.name", "ASC")
            ->contain(['AlbumReviewSnapshots', 'Artists']);
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

    public function getExpired($timeout = 0, $limit = 200)
    {
        return $this->find()
            ->where(['LastfmAlbums.modified < ' => $timeout])
            ->orWhere(['LastfmAlbums.modified IS NULL'])
            ->contain(['LastfmAlbums', 'Artists', 'Tracks'])
            ->limit($limit);
    }
}
