<?php
date_default_timezone_set('GMT');

include "../vendor/autoload.php";
include "../src/TimeTracker.php";
include "../src/TimeTrackerReporting.php";

$reporting = new TimeTrackerReporting;
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
            foreach ($today->data as $task):
                ?>
                <p>
                <strong>
                    (<?=$today->length?>)
                    <?=$today->task;?>
                </strong>
                <em><?=$today->notes;?></em>
                </p>

                <?php
            endforeach;
        ?>
    </body>
</html>