<?php

date_default_timezone_set('GMT');

include "vendor/autoload.php";
include "src/TimeTracker.php";
$TimeTracker = new TimeTracker;
$TimeTracker->getSearch();
