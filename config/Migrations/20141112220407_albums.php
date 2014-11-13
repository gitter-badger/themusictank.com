<?php

use Phinx\Migration\AbstractMigration;

class Albums extends AbstractMigration
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
        $this->table('albums')->addColumn('name', 'string', ['default' => ''])
            ->addColumn('artist_id', 'integer')
            ->addColumn('slug', 'string', ['default' => ''])
            ->addColumn('image', 'string', ['default' => ''])
            ->addColumn('image_src', 'string', ['default' => ''])
            ->addColumn('release_date', 'datetime', ['default' => null])
            ->addColumn('release_date_text', 'string', ['default' => null])
            ->addColumn('is_newrelease', 'boolean', ['default' => 0])
            ->addColumn('notability', 'integer', ['length' => 2, 'default' => 0])
            //->addForeignKey('artist_id', 'artists', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
            ->addIndex(['slug'], ['unique' => true])
            ->addIndex(['name'])
            ->save();
    }
}
