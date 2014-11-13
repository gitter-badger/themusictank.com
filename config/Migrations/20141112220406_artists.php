<?php

use Phinx\Migration\AbstractMigration;

class Artists extends AbstractMigration
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
        $table = $this->table('artists');
        $table->addColumn('name', 'string', ['default' => ''])
            ->addColumn('slug', 'string', ['default' => ''])
            ->addColumn('image', 'string', ['default' => ''])
            ->addColumn('image_src', 'string', ['default' => ''])
            ->addIndex(array('slug'), array('unique' => true))
            ->save();
    }
}
