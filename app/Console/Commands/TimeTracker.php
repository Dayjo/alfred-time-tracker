<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TimeTracker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timetracker {action} {query}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract all legacy documents that need extracting';

    public function handle()
    {
        $action = $this->argument('action');
        $query  = $this->argument('query');

        $timeTracker = new \App\Services\TimeTracker\TimeTracker;
        switch (trim($action)) {
            default:
            break;

            case 'search':
            $timeTracker->getSearch($query);
            break;

            case 'run':
            $timeTracker->getRun($query);
            break;
        }
    }
}
