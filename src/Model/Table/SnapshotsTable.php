<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use App\Model\Entity\ReviewSnapshot;

class SnapshotsTable extends Table {

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
            ->where(['updated' => null])
            ->orWhere(['updated <' => ReviewSnapshot::getExpiredRange()]);

        foreach ($query as $row) {
            $returnIds[] = $row->{$colName};
        }

        return $returnIds;
    }

}
