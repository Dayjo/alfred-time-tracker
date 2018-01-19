<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TimeTracker\TimeTrackerReporter;

class Reporting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reporting {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract all legacy documents that need extracting';

    public function handle()
    {
        $action = $this->argument('action');

        $reporter = new TimeTrackerReporter;

        if ($action == 'stop') {
            $status = $reporter->stopReportingServer();
            echo $status;
        } elseif ($action == 'start') {
            $status = $reporter->startReportingServer();
            echo $status;
        } elseif ($action == 'status') {
            $status = $reporter->reportingServerStatus();
            echo $status;
        } else {
            return "Unknown command " . $action;
        }
    }
}
