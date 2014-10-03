<?php

namespace App\Shell\Task;

use Cake\ORM\TableRegistry;
use Cake\Console\Shell;

class UpdateSongChallengeTask extends Shell {

    public function execute()
    {
        $this->out("Updating <comment>daily song challenge</comment>...");

        $taskTable = TableRegistry::get('Tasks');
        $task = $taskTable->getByName("last_trackchallenge");

        if ($task->requiresUpdate()) {
            $track = TableRegistry::get('Tracks')->findNewDailyChallenger();
            if ($track) {
                $this->out(sprintf("\tThe new daily challenger is '%s'.", $track->title));
                $taskTable->touch($task);
                $taskTable->save($task);
                $this->out("\t<info>Completed</info>");
                return;
            }
        }

        $this->out("\tTask did nothing.");
    }
}
