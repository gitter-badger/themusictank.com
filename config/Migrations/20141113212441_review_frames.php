<?php

use Phinx\Migration\AbstractMigration;

class ReviewFrames extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
     */
    public function change()
    {
        $this->table('review_frames')
            ->addColumn('artist_id',      'integer')
            ->addColumn('album_id',      'integer')
            ->addColumn('track_id',      'integer')
            ->addColumn('user_id',      'integer')
            ->addColumn('review_id',         'string',  ['length' => 13, 'default' => ''])
            ->addColumn('groove',          'float')
            ->addColumn('starpowering',    'boolean')
            ->addColumn('suckpowering',    'boolean')
            ->addColumn('multiplier',         'integer',  ['length' => 1])
            ->addColumn('position',         'integer',  ['length' => 6])
            ->addColumn('created',        'datetime', ['default' => NULL])
            ->addIndex(['artist_id'], ['unique' => true])
            ->addIndex(['album_id'], ['unique' => true])
            ->addIndex(['track_id'], ['unique' => true])
            ->addIndex(['user_id'], ['unique' => true])
            //->addForeignKey('artist_id', 'artists', 'id')
            //->addForeignKey('album_id', 'albums', 'id')
            //->addForeignKey('track_id', 'tracks', 'id')
            //->addForeignKey('user_id', 'users', 'id')
            ->save();
    }
}
