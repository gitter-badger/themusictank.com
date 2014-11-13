<?php

use Phinx\Migration\AbstractMigration;

class LastfmArtists extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     */
    public function change()
    {
        $this->table('lastfm_artists')
            ->addColumn('artist_id', 'integer')
            ->addColumn('mbid',         'string',  ['length' => 36, 'default' => null])
            ->addColumn('is_popular',   'boolean', ['default' => false])
            ->addColumn('url',          'string',  ['length' => 255, 'default' => null])
            ->addColumn('biography',         'text')
            ->addColumn('biogrpahy_curated', 'text')
            ->addColumn('created',        'datetime', ['default' => NULL])
            ->addColumn('modified',       'datetime', ['default' => NULL])
            ->addColumn('modified_discography', 'datetime', ['default' => NULL])
    //        ->addForeignKey('artist_id', 'artists', 'id')
            ->addIndex(['artist_id'], ['unique' => true])
            ->create();
    }
}
