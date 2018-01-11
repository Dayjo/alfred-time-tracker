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
        'gistAccessToken' => null
    ];

    private $logPath       = __DIR__  . '/../logs/';
    private $reportPath   = __DIR__  . '/../reports/';
    private $configPath    = __DIR__  . '/../config/';

    public function __construct()
    {
        // Grab the current log
        $this->logFiles[date('Y-m-d')] = new JSON($this->logPath . date('Y') . '/' . date('M') .'/log_' . date('Y-m-d') . '.json');
        if (empty($this->logFiles[date('Y-m-d')]->data)) {
            $this->logFiles[date('Y-m-d')]->data = array();
        }

        // Grab all of the existing tasks
        $this->tasksFile = new JSON($this->logPath . 'tasks.json');

        $this->Workflow = new Workflow($this->configTemplate, $this->configPath . '/config.json');
    }

    /**
     * Handle the search command
     * @return [type] [description]
     */
    public function getSearch()
    {

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
                $commands = [
                    'stop' => [
                        'title' => "Stop Tracking",
                        'arg' => ":stop",
                        'autocomplete' => ":stop"
                    ],
                    // 'report' => [
                    //     'title' => "Generate a Report",
                    //     'arg' => ":report",
                    //     'autocomplete' => ":report"
                    // ],
                    'open'=> [
                        'title' => "Open Workflow Folder",
                        'arg' => ":open",
                        'autocomplete' => ":open"
                    ],
                    'backup'=> [
                        'title' => "Backup your time logs",
                        'arg' => ":backup",
                        'autocomplete' => ":backup"
                    ],
                    'today'=> [
                        'title' => "Todays logs",
                        'arg' => ":today",
                        'autocomplete' => ":today"
                    ],
                    'clearTasks' => [
                        'title' => "Clear out cached tasks.",
                        'arg'   => ':clearTasks',
                        'autocomplete' => ':clearTasks',
                        'subtitle' => "This will reset any task names you autocomplete."
                    ],

                    'reporting' => [
                        'title' => "Reporting Server",
                        'arg'   => ':reporting',
                        'autocomplete' => ':reporting',
                        'subtitle' => "Start or stop the reporting server"
                    ]
                ];

                // Create a new Item List
                $List = new ItemList;

                // Loop through all of the existing task names
                foreach ($commands as $cmd => $item) {

                    // If the input matches the task name, output the task
                    if (trim($input) == '' || (stristr($cmd, $input) && $cmd != $input)) {

                        // Add the new item to the list
                        $List->add(new Item($item));
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
            'prefix' => ':reporting',
            'command' => function ($input) {

                // First lets see if the reporting server is running or not
                $running = trim(exec('ps -A | grep alfred-time-tracker | grep php | grep -m1 -v  -e "search.php" -e "run.php"'));


                if ($running) {
                    $actions = ['Stop', 'Open'];
                } else {
                    $actions = ['Start'];
                }

                // Create a new Item List
                $List = new ItemList;

                // Loop through all of the existing task names
                foreach ($actions as $action) {

                    // If the input matches the task name, output the task
                    if (trim($input) == '' || (stristr($action, $input) && $action != $input)) {

                        // Add the new item to the list
                        $List->add(new Item([
                            'title' => $action .' Reporting Server',
                            'arg' => ':reporting ' . $action,
                            'autocomplete' => ':reporting ' . $action])
                        );
                    }
                }


                // Output the list of tasks to
                echo $List->output();
            }
          ]
        ));

        // /**
        //  * Add the command for generating reports
        //  */
        // $this->Workflow->addCommand(new Command(
        //   [
        //     'prefix' => ':report',
        //     'command' => function ($input) {
        //         $reports = ['monthly','yearly'];
        //         // Create a new Item List
        //         $List = new ItemList;
        //
        //         // Loop through all of the existing task names
        //         foreach ($reports as $report) {
        //
        //             // If the input matches the task name, output the task
        //             if (trim($input) == '' || (stristr($report, $input) && $report != $input)) {
        //
        //                 // Add the new item to the list
        //                 $List->add(new Item([
        //                     'title' => 'Generate ' . $report. ' report',
        //                     'arg' => ':report ' . $report,
        //                     'autocomplete' => ':report ' . $report])
        //                 );
        //             }
        //         }
        //
        //
        //         // Output the list of tasks to
        //         echo $List->output();
        //     }
        //   ]
        // ));




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


                // Add the new item to the list
                $List->add(new Item([
                    'title' => "Todays Logs",
                    'arg' => ':today',
                    'autocomplete' => ':today'])
                );

                foreach ($report as $date => $day) {
                    $date = DateTime::createFromFormat('Y-m-d', $date);
                    $reportText .= "\n=============================\n";
                    $reportText .= '# ' . $date->format("l jS \of F Y") . "\n\n";
                    foreach ($day as $logItem) {
                        // Add the new item to the list
                        $List->add(new Item([
                            'title' => $logItem->task .  " (" . ($logItem->length > 1 ?  $this->secondsToTime($logItem->length) : 'on going...') . ")",
                            'arg' => ':today',
                            'autocomplete' => ':today'])
                        );
                    }
                }

                // Output the list of tasks to
                echo $List->output();
            }

        ]));

        /**
         * Add the command for backing up logs
         */
        $this->Workflow->addCommand(new Command(
          [
            'prefix' => ':backup',
            'command' => function ($input) {
                // Create a new Item List
                $List = new ItemList;

                $List->add(new Item([
                    'title' => "Backup your time logs",
                    'arg' => ':backup',
                    'autocomplete' => ':backup'])
                );
                // Output the list of tasks to
                echo $List->output();
            }

        ]));


        $this->Workflow->addCommand(new Command(
            [
                'prefix' => ':clearTasks',
                'command' => function ($input) {
                    // Create a new Item List
                    $List = new ItemList;

                    $List->add(new Item([
                        'title' => "Clear out cached tasks.",
                        'arg'   => ':clearTasks',
                        'autocomplete' => ':clearTasks',
                        'subtitle' => "This will reset any task names you autocomplete."])
                    );
                    // Output the list of tasks to
                    echo $List->output();
                }
            ]
        ));
    }

    /**
     * Backs up all logs into yearly gists
     * @return [type] [description]
     */
    private function backupLogs()
    {
        $logs[$year] = $this->getDirContents($this->logPath);

        $backup = [];
        foreach ($logs as $year) {
            foreach ($year as $fname) {
                $backup[basename($fname)] = ['content' => file_get_contents($fname)];
            }
        }

        // Backup the tasks.json too
        $backup['tasks.json'] = ['content' => file_get_contents($this->logPath . 'tasks.json')];

        /* Loop through all log directories */
        $githubClient = new \Github\Client();
        $githubClient->authenticate($this->Workflow->config->gistAccessToken, null, Github\Client::AUTH_URL_TOKEN);

        // Create a new gist
        $data = array(
            'files' => $backup,
            'public' => false,
            'description' => 'Backup of Time Tracker logs as of ' . date('Y-m-d H:i:s')
        );

        // First check to see if we already have a backup gist.
        if (empty($this->Workflow->config->backupGistId)) {
            $gist = $githubClient->api('gists')->create($data);
            $this->Workflow->config->backupGistId = $gist['id'];
        } else {
            $gist = $githubClient->api('gists')->update($this->Workflow->config->backupGistId, $data);
        }

        echo $gist['html_url'];
    }

    /**
     * [private description]
     * @var [type]
     */
    private function generateReport(string $type)
    {
        $report = [];
        $reportText = '';

        switch ($type) {
            case 'monthly':
                $reportName = date('Y-m');
                $logsDir = $this->logPath . date('Y') . '/' . date('M') . '/';
            break;

            case 'yearly':
                $reportName = date('Y');
                $logsDir = $this->logPath . date('Y') . '/';
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

        // Now loop through the report and write it to a file
        foreach ($report as $date => $day) {
            $date = DateTime::createFromFormat('Y-m-d', $date);
            $reportText .= "\n-------\n";
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
        if (!file_exists($this->reportPath)) {
            mkdir($this->reportPath);
        }

        // Write the report
        file_put_contents($this->reportPath. $reportName . '.md', $reportText);

        // Backup the report to a gist
        if ($this->Workflow->config->gistAccessToken) {
            $githubClient = new \Github\Client();
            $githubClient->authenticate($this->Workflow->config->gistAccessToken, null, Github\Client::AUTH_URL_TOKEN);

            // Create a new gist
            $data = array(
                'files' => [
                     $reportName . '.md' => ['content' => $reportText]
                    ],
                'public' => false,
                'description' => 'Time Tracking Report ' . date('Y-m-d H:i:s')
            );

            // First check to see if we already have a backup gist.
            if (empty($this->Workflow->config->reportsGistId)) {
                $gist = $githubClient->api('gists')->create($data);
                $this->Workflow->config->reportsGistId = $gist['id'];
            } else {
                $gist = $githubClient->api('gists')->update($this->Workflow->config->reportsGistId, $data);
            }


            echo $gist['html_url'];
            return;
        }

        echo $this->reportPath. $reportName . '.md';
        return;
    }

    /**
     * Initialises the report command
     */
    private function initRunReports()
    {



        //
        // /**
        //  * Add the command for generating reports
        //  */
        // $this->Workflow->addCommand(new Command(
        //   [
        //     'prefix' => ':report',
        //     'command' => function ($input) {
        //         $reports = ['monthly','yearly'];
        //
        //         // If running the command
        //         if ($input) {
        //             // echo "Generating {$input} Report!";
        //
        //             switch ($input) {
        //                 case 'monthly':
        //                 case 'yearly':
        //                     $this->generateReport($input);
        //                 break;
        //
        //             }
        //         }
        //     }
        //   ]
        // ));
    }

    public function initRunTasks()
    {



        /**
         * Add the command for starting report server
         */
        $this->Workflow->addCommand(new Command(
          [
            'prefix' => ':reporting',
            'command' => function ($input) {
                echo "GOT IN HERE "  . $input;
                switch (strtolower($input)) {
                    case 'start':
                        echo "Starting server..";
                        $this->startReportingServer();
                    break;

                    case 'stop':
                        echo "Stopping server..";
                        $this->stopReportingServer();
                    break;
                }
            }
        ]));
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
                echo "$input";
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


        /**
         * Add the command for backing up logs
         */
        $this->Workflow->addCommand(new Command(
          [
            'prefix' => ':backup',
            'command' => function ($input) {
                echo $this->backupLogs();
            }
        ]));

        /**
         * Add the command for clearing tasks
         */
        $this->Workflow->addCommand(new Command(
          [
            'prefix' => ':clearTasks',
            'command' => function ($input) {
                $this->clearTasks();
            }
        ]));
    }


    /**
     * Empty the tasks file
     * @return
     */
    private function clearTasks()
    {
        $this->tasksFile->data = [];
        echo "Cleared existing task names";
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
                $JSON = new JSON($this->logPath . 'tasks.json');
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

    private function startReportingServer()
    {
        $cmd = 'nohup php -S localhost:8000 -t "${PWD}/alfred-time-tracker/reporting" > /dev/null 2>&1 &';
        echo exec($cmd);
    }

    private function stopReportingServer()
    {
        // ps -A | grep -m1 alfred-time-tracker | grep -m1 php | grep -m1 -v  -e "search.php" | awk '{print $1}')
        $cmd = 'kill -9 $(ps -A | grep -m1 alfred-time-tracker | grep -m1 php | grep -m1 -v  -e "search.php" -e "run.php" | awk \'{print $1}\')';
        var_dump($cmd);
        exec($cmd, $output, $line);
        var_dump($output);
        var_dump($line);
    }
}
