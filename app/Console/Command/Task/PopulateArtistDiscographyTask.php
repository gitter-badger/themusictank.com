<?php
class PopulateArtistDiscographyTask extends Shell {

    public $uses = array('Artist', 'Album');

    public function execute()
    {
        $expiredIds = $this->Artist->query("
            SELECT
                Artist.id
            FROM artists as Artist
            WHERE Artist.id NOT IN (SELECT artist_id FROM albums)
            LIMIT 200;
        ");

        $this->out(sprintf("Found <comment>%s artist discographies</comment> that are out of sync.", count($expiredIds)));

        if(count($expiredIds))
        {
            $expiredArtists = $this->Artist->findAllById(Hash::extract($expiredIds, "{n}.Artist.id"));
            foreach ($expiredArtists as $artist)
            {
                $this->Album->data = $artist;
                $this->out(sprintf("\t<info>%d\t%s</info>", $artist["Artist"]["id"], $artist["Artist"]["name"]));
                $this->Album->updateDiscography($artist["Artist"]["name"]);
            }
        }
    }
}
