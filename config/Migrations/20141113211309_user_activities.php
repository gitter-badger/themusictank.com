<?php

use Phinx\Migration\AbstractMigration;

class UserActivities extends AbstractMigration
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
        $this->table('user_activities')
            ->addColumn('user_id', 'integer')
            ->addColumn('related_model_id', 'integer')
            ->addColumn('type',         'string',  ['length' => 100, 'default' => ''])
            ->addColumn('created',      'datetime', ['default' => NULL])
            //->addForeignKey('user_id', 'users', 'id')
            ->save();
    }
}
