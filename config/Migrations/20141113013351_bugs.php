<?php

use Phinx\Migration\AbstractMigration;

class Bugs extends AbstractMigration
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
        $this->table('bugs')
            ->addColumn('reporter_id', 'integer')
            ->addColumn('user_id', 'integer')
            ->addColumn('is_fixed',     'boolean', ['default' => 0])
            ->addColumn('type',         'string',  ['default' => ''])
            ->addColumn('location',     'string',  ['default' => ''])
            ->addColumn('details',      'text')
            ->addColumn('created',      'datetime', ['default' => NULL])
            //->addForeignKey('reporter_id', 'users', 'id')
            //->addForeignKey('user_id', 'users', 'id')
            ->save();
    }
}
