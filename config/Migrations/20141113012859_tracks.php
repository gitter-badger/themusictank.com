<?php

use Phinx\Migration\AbstractMigration;

class Tracks extends AbstractMigration
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
        $this->table('tracks')
            ->addColumn('album_id', 'integer')
            ->addColumn('title', 'string', ['default' => ''])
            ->addColumn('slug', 'string', ['default' => ''])
            ->addColumn('track_num', 'integer', ['length' => 3,  'default' => null])
            ->addColumn('duration', 'integer', ['length' => 6,  'default' => null])
            ->addColumn('is_challenge', 'boolean', ['default' => null])
            ->addColumn('wavelength', 'text')
            ->addIndex(['slug'], ['unique' => true])
            ->addIndex(['title'])
            //->addForeignKey('album_id', 'albums', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
            ->save();
    }
}
