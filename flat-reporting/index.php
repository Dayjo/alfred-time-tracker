<?php
date_default_timezone_set('GMT');

include "../vendor/autoload.php";
include "../src/TimeTracker.php";
include "../src/TimeTrackerReporting.php";

$reporting = new TimeTrackerReporting;
$reporting->initReporting();
$currentlyTracking =$reporting->currentlyTracking();
$today = $reporting->getLog(date("Y-m-d"));
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
    </head>
    <body>
        <h2>
            Currently Tracking:
        </h2>
        <strong>
            (<?=$currentlyTracking->length?>)
            <?=$currentlyTracking->task;?>
        </strong>
        <em><?=$currentlyTracking->notes;?></em>


        <h2>Today:</h2>

        <?php
        $lastTime = null;
            foreach ($today->data as $task):

                ?>
                <p>
                <strong>
                    <?=date('H:i:s', $task->time);?>
                     -&gt;

                     <?=date('H:i:s', $task->time + $task->length);?>
                    (<?=$reporting->secondsToTime($task->length);?>)
                    <?=$task->task;?>
                </strong>
                <em><?=$task->notes;?></em>
                </p>

                <?php
            endforeach;
        ?>
    </body>
</html>