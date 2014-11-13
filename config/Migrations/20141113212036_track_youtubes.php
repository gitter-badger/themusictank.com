<?php

use Phinx\Migration\AbstractMigration;

class TrackYoutubes extends AbstractMigration
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
        $this->table('track_youtubes')
            ->addColumn('track_id', 'integer')
            ->addColumn('youtube_key', 'string', ['default' => ''])
            ->addColumn('youtube_key_manual', 'string', ['default' => null])
            ->addColumn('waveform',      'text')
            //->addForeignKey('track_id', 'tracks', 'id')
            ->save();
    }
}
