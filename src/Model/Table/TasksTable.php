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
                ],
                'Task.started' => [
                    'modified' => 'always'
                ],
                'Task.ended' => [
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

    public function setCronStart($which)
    {
        $task = $this->getByName($which);
        return $this->touch($task, 'Task.started');
    }

    public function setCronEnd($which)
    {
        $task = $this->getByName($which);
        return $this->touch($task, 'Task.ended');
    }
}
