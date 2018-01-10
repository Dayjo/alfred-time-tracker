<?php
use Alfred\Workflow as Workflow;
use Alfred\Command as Command;
use Alfred\ItemList as ItemList;
use Alfred\Item as Item;
use Dayjo\JSON as JSON;

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);


class TimeTracker
{
    private $Workflow;
    private $logFiles;
    private $tasksFile;
    private $configTemplate = [
        'dayEnds' => '18:00',
    ];

    public function __construct()
    {
        // Grab the current log
        $this->logFiles[date('Y-m-d')] = new JSON(__DIR__ . '/../logs/' . date('Y') . '/' . date('M') .'/log_' . date('Y-m-d') . '.json');
        if (empty($this->logFiles[date('Y-m-d')]->data)) {
            $this->logFiles[date('Y-m-d')]->data = array();
        }



        // Grab all of the existing tasks
        $this->tasksFile = new JSON(__DIR__ . '/../logs/tasks.json');

        $this->Workflow = new Workflow($this->configTemplate, __DIR__ . '/../config/config.json');
    }

    /**
     * Handle the search command
     * @return [type] [description]
     */
    public function getSearch()
    {
        $githubClient = new \Github\Client();
        $repositories = $githubClient->api('user')->repositories('dayjo');
        var_dump($repositories);
        exit;

        // Itialise the commands
        $this->Workflow->state = $this->Workflow::STATE_SEARCHING;
        $this->initTasks();
        $this->initCommands();
        $this->Workflow->run();
    }

    /**
     * Handle the run command
     */
    public function getRun()
    {
        $this->Workflow->state = $this->Workflow::STATE_RUNNING;
        $this->initRunTasks();
        $this->initRunReports();
        $this->Workflow->run();
    }

    private function getLatestWorkflowVersion()
    {
        // First check for update
        $request = file_get_contents("https://packagist.org/packages/dayjo/alfred-time-tracker.json");
        $package = json_decode($request, 1);
        $versions = $package['package']['versions'];
        // exec('git describe --abbrev=0');
        foreach ($versions as $v => $version) {
            if ($v[0] != 'v') {
                continue;
            } else {
                $latest = $v;
                break;
            }
        }

        return $latest;
    }

    /**
     * Initialises the report command
     */
    private function initCommands()
    {
        /**
         * Add the command for generating reports
         */
        $this->Workflow->addCommand(new Command(
          [
            'prefix' => ':',
            'command' => function ($input) {
                $commands = ['stop', 'report', 'open'];

                // Create a new Item List
                $List = new ItemList;

                // Loop through all of the existing task names
                foreach ($commands as $cmd) {

                    // If the input matches the task name, output the task
                    if (trim($input) == '' || (stristr($cmd, $input) && $cmd != $input)) {

                        // Add the new item to the list
                        $List->add(new Item([
                            'title' => $cmd,
                            'arg' => ":{$cmd}",
                            'autocomplete' => ":". $cmd])
                        );
                    }
                }


                // Output the list of tasks to
                echo $List->output();
            }
          ]
        ));

        /**
         * Add the command for generating reports
         */
        $this->Workflow->addCommand(new Command(
          [
            'prefix' => ':stop',
            'command' => function ($input) {
                // Create a new Item List
                $List = new ItemList;

                // Add the new item to the list
                $List->add(new Item([
                    'title' => 'Stop Tracking ' . $this->currentlyTracking,
                    'arg' => ':stop',
                    'autocomplete' => ':stop'])
                );
                // Output the list of tasks to
                echo $List->output();
            }
        ]));


        /**
         * Add the command for generating reports
         */
        $this->Workflow->addCommand(new Command(
          [
            'prefix' => ':note',
            'command' => function ($input) {
                // Create a new Item List
                $List = new ItemList;

                foreach ($this->logFiles[date('Y-m-d')]->data as $log) {
                    // Add the new item to the list
                    $List->add(new Item([
                        'title' => date("H:i:s", $log->time) . " " . $log->task,
                        'arg' => ':note ' .  $log->time . substr($input, strlen($log->task)),
                        'autocomplete' => ':note ' . $log->task
                        ])
                    );
                }

                // Output the list of tasks to
                echo $List->output();
            }
        ]));

        /**
         * Add the command for generating reports
         */
        $this->Workflow->addCommand(new Command(
          [
            'prefix' => ':report',
            'command' => function ($input) {
                $reports = ['monthly','yearly'];
                // Create a new Item List
                $List = new ItemList;

                // Loop through all of the existing task names
                foreach ($reports as $report) {

                    // If the input matches the task name, output the task
                    if (trim($input) == '' || (stristr($report, $input) && $report != $input)) {

                        // Add the new item to the list
                        $List->add(new Item([
                            'title' => 'Generate ' . $report. ' report',
                            'arg' => ':report ' . $report,
                            'autocomplete' => ':report ' . $report])
                        );
                    }
                }


                // Output the list of tasks to
                echo $List->output();
            }
          ]
        ));



        /**
         * Add the command for listing todays logs
         */
        $this->Workflow->addCommand(new Command(
          [
            'prefix' => ':today',
            'command' => function ($input) {
                // Create a new Item List
                $List = new ItemList;
                $report = [];
                $day = date('Y-m-d');
                // Loop through todays logs
                $report[$day] = [];
                $previousTime = 0;
                foreach ($this->logFiles[date('Y-m-d')]->data as $logItem) {
                    // Add this item to the report
                    $report[$day][] = $logItem;

                    // Set the previous item's length
                    if ($previousTime) {
                        if ($logItem->time > $previousTime && $logItem->time - $previousTime) {
                            $report[$day][count($report[$day])-2]->length = $logItem->time - $previousTime;
                        }
                    }

                    $previousTime = $logItem->time;
                }




                foreach ($report as $date => $day) {
                    $date = DateTime::createFromFormat('Y-m-d', $date);
                    $reportText .= "\n=============================\n";
                    $reportText .= '# ' . $date->format("l jS \of F Y") . "\n\n";
                    foreach ($day as $logItem) {
                        // Add the new item to the list
                        $List->add(new Item([
                            'title' => $logItem->task .  " (" . ($logItem->length > 1 ?  $this->secondsToTime($logItem->length) : 'on going...') . ")",
                            'arg' => '',
                            'autocomplete' => ''])
                        );
                    }
                }

                // Output the list of tasks to
                echo $List->output();
            }

        ]));
    }

    /**
     * Initialises the report command
     */
    private function initRunReports()
    {

        /**
         * Add the command for generating reports
         */
        $this->Workflow->addCommand(new Command(
          [
            'prefix' => ':report',
            'command' => function ($input) {
                $reports = ['monthly','yearly'];

                // If running the command
                if ($input) {
                    // echo "Generating {$input} Report!";

                    switch ($input) {
                        case 'monthly':
                            $reportName = date('Y-m');
                            $logsDir = __DIR__ . '/../logs/' . date('Y') . '/' . date('M') . '/';
                        break;

                        case 'yearly':
                            $reportName = date('Y');
                            $logsDir = __DIR__ . '/../logs/' . date('Y') . '/';
                        break;
                    }

                    // Get all the of the log files for the requested period
                    $logFiles = $this->getDirContents($logsDir);

                    // Sort them in date order
                    usort($logFiles, function ($a, $b) {
                        $aname = pathinfo($a, PATHINFO_FILENAME);
                        $bname = pathinfo($b, PATHINFO_FILENAME);

                        if ($aname > $bname) {
                            return 1;
                        } else {
                            return -1;
                        }
                    });
                    $report = [];
                    $reportText = '';
                    // Loop through the logs, load them in and build the report
                    foreach ($logFiles as $log) {
                        $filename = pathinfo($log, PATHINFO_FILENAME);
                        $day = str_replace('log_', '', $filename);
                        $day = str_replace('.json', '', $day);

                        $date = DateTime::createFromFormat('Y-m-d', $day);

                        $file = new JSON($log);

                        $report[$day] = [];
                        $previousTime = 0;
                        foreach ($file->data as $logItem) {
                            // Add this item to the report
                            $report[$day][] = $logItem;

                            // Set the previous item's length
                            if ($previousTime) {
                                if ($logItem->time > $previousTime && $logItem->time - $previousTime) {
                                    $report[$day][count($report[$day])-2]->length = $logItem->time - $previousTime;
                                }
                            }

                            $previousTime = $logItem->time;
                        }

                        // Set the last item's length
                        if ($previousTime) {
                            if ($previousTime < strtotime($day . ' ' . $this->Workflow->config->dayEnds)) {
                                $report[$day][count($report[$day])-1]->length = strtotime($day . ' ' . $this->Workflow->config->dayEnds) - $previousTime;
                            } else {
                                $report[$day][count($report[$day])-1]->length =  null;
                            }
                        }
                    }

                    foreach ($report as $date => $day) {
                        $date = DateTime::createFromFormat('Y-m-d', $date);
                        $reportText .= "\n=============================\n";
                        $reportText .= '# ' . $date->format("l jS \of F Y") . "\n\n";
                        foreach ($day as $logItem) {
                            $reportText .= '## ' . $logItem->task . "\n";

                            if ($logItem->notes) {
                                $reportText .= 'Notes: ' . $logItem->notes . " \n\n";
                            }

                            $reportText .= '* Started: ' . date('H:i:s', $logItem->time). "\n";

                            if ($logItem->length) {
                                $reportText .= '* Length: ' . $this->secondsToTime($logItem->length). "\n\n";
                            } else {
                                $reportText .= '* Unknown Length' . "\n\n";
                            }
                        }
                    }

                    // Create the reports dir if it doesn't exist
                    if (!file_exists(__DIR__ . '/../reports/')) {
                        mkdir(__DIR__ . '/../reports/');
                    }

                    // WRite the report
                    file_put_contents(__DIR__ . '/../reports/'. $reportName . '.md', $reportText);

                    // Output the filename so that it opens
                    echo __DIR__ . '/../reports/'. $reportName . '.md';
                }
            }
          ]
        ));
    }

    public function initRunTasks()
    {
        /**
         * The actual start tracking command
         */
        $this->Workflow->addCommand(new Command(
          [
            'prefix' => ':start',
            'command' => function ($input) {

                // If no input, just return false
                if (!$input) {
                    return false;
                }

                $this->track($input);
                echo "Started Tracking $input";
            }
          ]
        ));

        /**
         * Add the command for stopping tracking
         */
        $this->Workflow->addCommand(new Command(
          [
            'prefix' => ':stop',
            'command' => function ($input) {
                $this->track('stop');
                echo "Stopped Tracking";
            }
          ]
        ));

        /**
         * Add the command for adding notes
         */
        $this->Workflow->addCommand(new Command(
          [
            'prefix' => ':note',
            'command' => function ($input) {
                $time = explode(' ', $input)[0];
                $notes = substr($input, strlen($time));

                foreach ($this->logFiles[date('Y-m-d', $time)]->data as &$log) {
                    if ($log->time == $time) {
                        $log->notes = trim($notes);
                    }
                }

                echo "Added notes!";
            }
        ]));
    }

    /**
     * Init the start task command list
     */
    public function initTasks()
    {
        $this->Workflow->addCommand(new Command(
          [
            'prefix' => '', // default (no extra command, i.e. "keyword myTask"
            'command' => function ($input) {

                // If no input, just return false
                if (!$input) {
                    return false;
                }

                // Load in the tasks json
                $JSON = new JSON(__dir__ . '/../logs/tasks.json');
                $tasks =& $JSON->data;

                // Create a new Item List
                $List = new ItemList;

                // Put the currently typed task name like a new task
                // Add the new item to the list
                $List->add(new Item([
                    'title' => 'Start Tracking "' . $input. '"',
                    'arg' => ':start ' . $input,
                    'autocomplete' => $input])
                );

                // Loop through all of the existing task names
                foreach ($tasks as $task) {

                    // If the input matches the task name, output the task
                    if (stristr($task, $input) && $task != $input) {

                        // Add the new item to the list
                        $List->add(new Item([
                            'title' => 'Start Tracking "' . $task. '"',
                            'arg' => ':start ' . $task,
                            'autocomplete' => $task])
                        );
                    }
                }

                // Add the currently tracked item
                $currentlyTracking = $this->currentlyTracking();
                $List->add(new Item([
                    'title' => "Currently Tracking {$currentlyTracking->task} {$currentlyTracking->length}",
                    'arg' => '',
                    'valid' => false
                ]));

                // Output the list of tasks to
                echo $List->output();
            }
          ]
        ));
    }

    /**
     * Return the currently tracked task
     * @return
     */
    private function currentlyTracking()
    {
        $currentlyTracking = $this->logFiles[date('Y-m-d')]->data[ count($this->logFiles[date('Y-m-d')]->data)-1 ];
        if ($currentlyTracking) {
            $currentlyTracking->length = $this->secondsToTime(time() - $currentlyTracking->time);
        }
        return $currentlyTracking;
    }


    /**
    * Write a log to today's log file
    * @param  string $text The task name to log
    */
    private function track($task)
    {
        $this->logFiles[date('Y-m-d')]->data[] = [ 'time' => time(), 'task' => $task, 'notes' => '' ];

        // Add $text to the list of tasks if it doesn't exist
        if (!in_array($task, array('stop')) && !in_array($task, $this->tasksFile->data)) {
            $this->tasksFile->data[] = $task;
        }
    }

    public function secondsToTime(int $seconds)
    {
        $dtF = new DateTime("@0");
        $dtT = new DateTime("@$seconds");
        $format = '%h:%I:%S';

        if ($seconds > 86400) {
            $format = '%D days %h:%I:%S';
        }
        return $dtF->diff($dtT)->format($format);
    }

    private function getDirContents($path)
    {
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

        $files = array();
        foreach ($rii as $file) {
            if (!$file->isDir()) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }
}
