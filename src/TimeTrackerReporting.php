<?php
use Dayjo\JSON as JSON;

class TimeTrackerReporting extends TimeTracker
{

    /**
     * Just start output the reporting page
     * @return [type] [description]
     */
    public function initReporting()
    {
        $this->generateReport('yearly');
    }
}
