<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use App\Model\Entity\Task;

class TasksTable extends Table {

    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'modified' => 'always'
                ]
            ]
        ]);
    }

    public function getByName($which)
    {
        $task = $this->findByName($which)->first();
        if (!$task) {
            return new Task(['name' => $which]);
        }
        return $task;
    }

    public function touch($which)
    {
        $task = $this->getByName($which);
        $task->modified = new \DateTime();
        $this->save($task);
        return $task;
    }
}
