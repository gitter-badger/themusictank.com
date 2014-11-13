<?php

use Phinx\Migration\AbstractMigration;

class UserAchievements extends AbstractMigration
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
        $this->table('user_achievements')
            ->addColumn('user_id', 'integer')
            ->addColumn('achievement_id', 'integer')
            ->addColumn('created',      'datetime', ['default' => NULL])
            //->addForeignKey('user_id', 'users', 'id')
            ->save();
    }
}
