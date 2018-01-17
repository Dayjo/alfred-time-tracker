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
            default:
            case 'all':
                $this->generateReport('alltime');
                $rangeTime = 0;
            break;

            case 'weekly':
                $this->generateReport('monthly');
                $rangeTime = '-7 day';
            break;

            case 'daily':
                //@TODO pull in the config for dayEnds
                $rangeTime = 'yesterday 18:00';
            break;
        }

        foreach ($this->logFiles as $date => $logFile) {
            if (strtotime($date) > strtotime($rangeTime)) {
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
}
