<?php

use Phinx\Migration\AbstractMigration;

class TrackReviewSnapshots extends AbstractMigration
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
        $this->table('user_track_review_snapshots')
            ->addColumn('track_id',      'integer')
            ->addColumn('total',          'integer',  ['length' => 11, 'default' => 0]) // unsigned not null
            ->addColumn('liking',         'integer',  ['length' => 11, 'default' => 0]) // unsigned not null
            ->addColumn('liking_pct',     'integer',  ['length' => 3, 'default' => 0]) // unsigned not null
            ->addColumn('disliking',      'integer',  ['length' => 11, 'default' => 0]) // unsigned not null
            ->addColumn('disliking_pct',  'integer',  ['length' => 3, 'default' => 0]) // unsigned not null
            ->addColumn('neutral',        'integer',  ['length' => 11, 'default' => 0]) // unsigned not null
            ->addColumn('neutral_pct',    'integer',  ['length' => 3, 'default' => 0]) // unsigned not null
            ->addColumn('curve',          'text')
            ->addColumn('highs',          'text')
            ->addColumn('lows',           'text')
            ->addColumn('score',          'float',    ['default' => NULL])
            ->addColumn('top',            'text')
            ->addColumn('bottom',         'text')
            ->addColumn('created',        'datetime', ['default' => NULL])
            ->addColumn('modified',       'datetime', ['default' => NULL])
            ->addIndex(['track_id'], ['unique' => true])
            //->addForeignKey('track_id', 'tracks', 'id')
            ->save();
    }
}
