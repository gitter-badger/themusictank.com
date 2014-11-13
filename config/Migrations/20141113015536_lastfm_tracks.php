<?php

use Phinx\Migration\AbstractMigration;

class LastfmTracks extends AbstractMigration
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
        $this->table('lastfm_tracks')
            ->addIndex(['track_id'], ['unique' => true])
            ->addColumn('mbid',         'string',  ['length' => 36, 'default' => null])
            ->addColumn('wiki',         'text')
            ->addColumn('wiki_curated', 'text')
            ->addColumn('url',         'string',  ['length' => 255, 'default' => ''])
            ->addColumn('created',        'datetime', ['default' => NULL])
            ->addColumn('modified',       'datetime', ['default' => NULL])
            ->addColumn('track_id', 'integer')
            //->addForeignKey('track_id', 'track', 'id')
            ->save();
    }
}
