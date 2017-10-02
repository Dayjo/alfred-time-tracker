<?php
use Alfred\Workflow as Workflow;
use Alfred\Command as Command;
use Alfred\ItemList as ItemList;
use Alfred\Item as Item;
use Dayjo\JSON as JSON;

class TimeTracker
{
    /**
     * Handle the search command
     * @return [type] [description]
     */
    public function getSearch()
    {
        $Workflow = new Workflow();

        $Workflow->addCommand(new Command(
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


        $Workflow->addCommand(new Command(
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
                            'autocomplete' => $report])
                        );
                    }
                }


                // Output the list of tasks to
                echo $List->output();
            }
          ]
        ));

        $Workflow->run();
    }
}
