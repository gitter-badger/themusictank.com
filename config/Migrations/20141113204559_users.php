<?php

use Phinx\Migration\AbstractMigration;

class Users extends AbstractMigration
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
        $this->table('users')
            ->addColumn('firstname',        'string',  ['default' => null])
            ->addColumn('lastname',        'string',  ['default' => null])
            ->addColumn('username',        'string',  ['default' => null])
            ->addColumn('password',        'string',  ['default' => null])
            ->addColumn('role',         'string',  ['length' => 10, 'default' => null])
            ->addColumn('created',      'datetime', ['default' => NULL])
            ->addColumn('updated',      'datetime', ['default' => NULL])
            ->addColumn('slug',        'string',  ['default' => null])
            ->addColumn('image_src',        'string',  ['default' => null])
            ->addColumn('image',        'string',  ['default' => null])
            ->addColumn('location',        'string',  ['default' => null])
            ->addIndex(['slug'], ['unique' => true])
            ->addIndex(['username'], ['unique' => true])
            ->save();
    }
}
