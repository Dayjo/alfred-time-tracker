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

    const STATE_SEARCHING = 1;
    const STATE_RUNNING = 1;

    public function __construct()
    {
        // Grab the current log
        $this->logFiles[date('Y-m-d')] = new JSON(__DIR__ . '/../logs/log_' . date('Y-m-d') . '.json');
        if (empty($this->logFiles[date('Y-m-d')]->data)) {
            $this->logFiles[date('Y-m-d')]->data = array();
        }

        // Grab all of the existing tasks
        $this->tasksFile = new JSON(__DIR__ . '/../logs/tasks.json');
    }

    /**
     * Handle the search command
     * @return [type] [description]
     */
    public function getSearch()
    {
        $this->state = static::STATE_SEARCHING;
        $this->Workflow = new Workflow();
        $this->initTasks();
        $this->initReports();
        $this->Workflow->run();
    }

    /**
     * Handle the run command
     */
    public function getRun()
    {
        $this->state = static::STATE_RUNNING;
        $this->Workflow = new Workflow();
        $this->initRunTasks();
        $this->initRunReports();
        $this->Workflow->run();
    }

    /**
     * Initialises the report command
     */
    private function initReports()
    {
        /**
         * Add the command for generating reports
         */
        $this->Workflow->addCommand(new Command(
          [
            'prefix' => ':',
            'command' => function ($input) {
                $commands = ['stop', 'report'];

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
            'prefix' => ':report',
            'command' => function ($input) {
                $reports = ['weekly', 'monthly','yearly'];
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
                $reports = ['weekly', 'monthly','yearly'];

                // If running the command
                if ($input) {
                    echo "Generating {$input} Report!";
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
                $List->add(new Item([
                    'title' => "Currently Tracking {$currentlyTracking} {$currentlyTrackingFor}",
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
}
