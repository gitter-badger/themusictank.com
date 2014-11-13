<?php

use Phinx\Migration\AbstractMigration;

class FacebookUsers extends AbstractMigration
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
        $this->table('facebook_users')
            ->addColumn('facebook_id',  'integer', ['length' => 11]) // unsigned not null
            ->addColumn('user_id', 'integer')
            //->addForeignKey('user_id', 'users', 'id')
            ->create();
    }
}
