<?php

use Phinx\Migration\AbstractMigration;

class Tasks extends AbstractMigration
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
        $this->table('tasks')
            ->addColumn('name',         'string',  ['default' => ''])
            ->addColumn('created',      'datetime', ['default' => NULL])
            ->addColumn('modified',      'datetime', ['default' => NULL])
            ->addIndex(['name'], ['unique' => true])
            ->save();
    }
}
