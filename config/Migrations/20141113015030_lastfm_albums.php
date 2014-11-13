<?php

use Phinx\Migration\AbstractMigration;

class LastfmAlbums extends AbstractMigration
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
        $this->table('lastfm_albums')
            ->addColumn('album_id',       'integer')
            ->addColumn('mbid',         'string',  ['length' => 36, 'default' => null])
            ->addColumn('wiki',         'text')
            ->addColumn('wiki_curated', 'text')
            ->addColumn('created',        'datetime', ['default' => NULL])
            ->addColumn('modified',       'datetime', ['default' => NULL])
            //->addForeignKey('album_id', 'albums', 'id')
            ->addIndex(['album_id'], ['unique' => true])
            ->create();
    }
}
