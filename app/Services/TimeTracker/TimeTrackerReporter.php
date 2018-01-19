<?php
namespace App\Services\TimeTracker;

class TimeTrackerReporter extends TimeTracker
{
    /**
     * Just start output the reporting page
     * @return [type] [description]
     */
    public function initReporting()
    {
        $this->generateReport('alltime');
    }

    public function totals($range = 'all', $sortBy = 'count')
    {
        switch ($range) {

            case 'all':
                $this->generateReport('alltime');
                $rangeTime = 0;
            break;

            case 'month':
                $this->generateReport('monthly');
                $rangeTime = 'first day of this month 00:00';
            break;


            case 'weekly':
                $this->generateReport('monthly');
                $rangeTime = 'last monday 00:00';
            break;

            case 'daily':
                //@TODO pull in the config for dayEnds
                $rangeTime = 'yesterday 18:00';
            break;

            default:
                //  a fixed date range
                $this->generateReport('alltime');
                //@TODO pull in the config for dayEnds
                // $rangeTime = "$range -1 day 18:00";

            break;
        }

        $totals = [];

        foreach (array_reverse($this->logFiles) as $date => $logFile) {
            if ((is_array($range) && strtotime($date) >= strtotime($range[0]) && strtotime($date) <= strtotime($range[1])) || (!is_array($range) && strtotime($date) > strtotime($rangeTime))) {
                foreach ($logFile->data as $log) {
                    if (empty($totals[$log->task])) {
                        $totals[$log->task] = ['length' => 0, 'count' => 0];
                    }

                    $totals[$log->task]['length'] += $log->length;
                    $totals[$log->task]['count'] ++;
                }
            }
        }

        uasort($totals, function ($a, $b) use ($sortBy) {
            if ($a[$sortBy] > $b[$sortBy]) {
                return -1;
            } elseif ($a[$sortBy] < $b[$sortBy]) {
                return 1;
            }

            return 0;
        });


        foreach ($totals as &$task) {
            $task['time'] = $this->secondsToTime($task['length'], '%h:%I');
        }

        return $this->totals = $totals;
    }

    /**
     * Simply return the list of logs day by day
     */
    public function timeline($range = 'all')
    {
        $this->generateReport('alltime');
        $timeline = [];

        switch ($range) {

            case 'all':
                $this->generateReport('alltime');
                $rangeTime = 0;
            break;

            case 'month':
                $this->generateReport('monthly');
                $rangeTime = 'first day of this month 00:00';
            break;


            case 'weekly':
                $this->generateReport('monthly');
                $rangeTime = 'last monday 00:00';
            break;

            case 'daily':
                //@TODO pull in the config for dayEnds
                $rangeTime = 'yesterday 18:00';
            break;
        }

        foreach ($this->logFiles as $date => $logFile) {
            // Is it within the specified date range?
            if ((is_array($range) && strtotime($date) >= strtotime($range[0]) && strtotime($date) <= strtotime($range[1])) || (!is_array($range) && strtotime($date) > strtotime($rangeTime))) {
                foreach ($logFile->data as $log) {
                    if (empty($timeline[$date])) {
                        $timeline[$date] = [];
                    }

                    if (empty($timeline[$date][$log->task])) {
                        $timeline[$date][$log->task] = [];
                    }

                    $timeline[$date][$log->task][] = $log;
                }
            }
        }

        // Order the array by date
        uksort($timeline, function ($a, $b) {
            return strtotime($a) - strtotime($b);
        });

        return $timeline;
    }
}
