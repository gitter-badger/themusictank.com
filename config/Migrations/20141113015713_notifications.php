<?php

use Phinx\Migration\AbstractMigration;

class Notifications extends AbstractMigration
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
        $this->table('notifications')
            ->addColumn('user_id', 'integer')
            ->addColumn('title',        'string',  ['default' => ''])
            ->addColumn('type',         'string',  ['default' => ''])
            ->addColumn('is_viewed',    'boolean', ['default' => false])
            ->addColumn('created',      'datetime', ['default' => NULL])
            //->addForeignKey('user_id', 'users', 'id')
            ->save();
    }
}
