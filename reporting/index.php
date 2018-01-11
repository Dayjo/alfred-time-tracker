<?php
date_default_timezone_set('GMT');

include "../vendor/autoload.php";
include "../src/TimeTracker.php";
include "../src/TimeTrackerReporting.php";

$reporting = new TimeTrackerReporting;

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
    </head>
    <body>
        <h2>Currently Tracking: <?=$reporting->currentlyTracking()->task;?></h2>
    </body>
</html>