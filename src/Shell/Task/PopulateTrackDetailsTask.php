<?php
namespace App\Shell\Task;

use Cake\ORM\TableRegistry;
use Cake\Console\Shell;

class PopulateTrackDetailsTask extends Shell {

    public function execute()
    {
        $this->out("Updating <comment>track details</comment>...");

        $taskTable = TableRegistry::get('Tasks');
        $task = $taskTable->getByName('track_details');

        if ($task->requiresUpdate()) {

            $tblTracks = TableRegistry::get('Tracks');
            $expiredTracks = $tblTracks->find('expired', ['timeout' => $task->getTimeout()])->all();

            if (count($expiredTracks)) {
                $this->out(sprintf("\tFound <comment>%s tracks</comment> that are out of sync.", count($expiredTracks)));

                foreach ($expiredTracks as $idx => $track) {
                    $this->out(sprintf("\t\t%d/%d\t%d <info>\t%s</info>...", $idx+1, count($expiredTracks), $track->id, $track->title));
                    $tblTracks->syncToRemote($track);
                }

            } else {
                $this->out("\tTrack details are up-to-date.");
            }

            $taskTable->touch('track_details');

        } else {
            $this->out("\tTrack details update is not ready to run.");
        }

        $this->out("\t<info>Completed.</info>");
    }
}
