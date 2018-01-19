<?php

date_default_timezone_set('GMT');

// include "vendor/autoload.php";
require __DIR__ . "/../app/Services/TimeTracker/TimeTracker.php";
$TimeTracker = new App\Services\TimeTracker\TimeTracker;
$TimeTracker->getSearch();
