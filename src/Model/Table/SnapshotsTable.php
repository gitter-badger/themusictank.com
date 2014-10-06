<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\Event\Event;

use App\Model\Entity\ReviewSnapshot;

class SnapshotsTable extends Table {

    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
    }

    private $_jsondFields = ["curve", "ranges", "highs", "lows", "top", "bottom"];

    public function afterFind($results, $primary = false)
    {

        debug($results);
/*
        if(!array_key_exists("id", $results))
        {
            foreach($results as $idx => $row)
            {
                if(array_key_exists($this->alias, $row))
                {
                    foreach ($this->_jsondFields as $field)
                    {
                        if(array_key_exists($field, $row[$this->alias]) && is_string($row[$this->alias][$field]))
                        {
                            $results[$idx][$this->alias][$field] = json_decode($row[$this->alias][$field]);
                        }
                    }
                }
            }
        }*/
        return $results;
    }

    public function beforeSave(Event $event, $entity)
    {
         foreach ($this->_jsondFields as $field) {
            if (!is_null($entity->{$field}) && !is_string($entity->{$field})) {
                $entity->{$field} = json_encode($entity->{$field});
            }
        }
    }

    public function getIdsWithNoSnapshots()
    {
        $colName = $this->getBelongsToPrefix() . "_id";
        $artistIds = $this->getIdList();
        $returnIds = [];

        $query = TableRegistry::get('review_frames')->find()->distinct([$colName]);


        if(count($artistIds)) {
            $query->where([$colName . ' NOT IN' => $artistIds]);
        }

        foreach ($query as $row) {
            $returnIds[] = $row->{$colName};
        }

        return $returnIds;
    }

    public function getMissingIds()
    {
        $tableName = Inflector::pluralize($this->getBelongsToPrefix());
        $artistIds = $this->getIdList();
        $returnIds = [];
        $query = TableRegistry::get($tableName)->find()->select(["id"]);

        if(count($artistIds)) {
            $query->where(['id NOT IN' => $artistIds]);
        }

        foreach ($query as $row) {
            $returnIds[] = $row->id;
        }

        return $returnIds;
    }

    public function getIdList()
    {
        $colName = $this->getBelongsToPrefix() . "_id";
        $ids = [];
        foreach ($this->find()->select([$colName]) as $row) {
            $ids[] = $row->{$colName};
        }
        return $ids;
    }

    public function getExpiredIds()
    {
        $colName = $this->getBelongsToPrefix() . "_id";
        $returnIds = [];
        $query = $this->find()
            ->select([$colName])
            ->where(['modified' => null])
            ->orWhere(['modified <' => ReviewSnapshot::getExpiredRange()]);

        foreach ($query as $row) {
            $returnIds[] = $row->{$colName};
        }

        return $returnIds;
    }

}
