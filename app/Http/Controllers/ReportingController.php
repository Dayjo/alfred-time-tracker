<?php
namespace App\Http\Controllers;

use App\Services\TimeTracker\TimeTrackerReporter;

class ReportingController extends Controller
{
    public function getDashboard()
    {
        $reporter = new TimeTrackerReporter;

        $reporter->initReporting();
        $today = $reporter->getLog(date("Y-m-d"));

        $totals = $reporter->totals('all');
        $currentlyTracking = $reporter->currentlyTracking();

        return view('dashboard')->with([
            'reporter' => $reporter,
            'currentlyTracking' => $currentlyTracking,
            'today' => $today,
            'totals' => $totals,
            'rangeStart' => date('Y-m-d', strtotime('first day of this year')),
            'rangeEnd' =>  date('Y-m-d'),
        ]);
    }

    /** For Alfred **/
    public function getSearch()
    {
        $timeTracker = new TimeTrackerReporter;
        return $timeTracker->getSearch();
    }

    /** For Alfred **/
    public function getRun()
    {
        $timeTracker = new TimeTrackerReporter;
        return $timeTracker->getRun();
    }

    public function getRangeReport($from, $to, $title = "this date range")
    {
        $reporter = new TimeTrackerReporter;

        if ($reporter->Workflow->config->dayEnds) {
            $to .= ' ' . $reporter->Workflow->config->dayEnds;
        }
        return view('report', [
            'totals' => $reporter->totals([date('Y-m-d H:i:s', strtotime($from)), date('Y-m-d H:i:s', strtotime($to))]),
            'timeline' => $reporter->timeline([date('Y-m-d H:i:s', strtotime($from)), date('Y-m-d H:i:s', strtotime($to))]),
            'reporter' => $reporter,
            'pieRange' => $from.','.$to,
            'rangeStart' => date('Y-m-d', strtotime($from)),
            'rangeEnd' =>  date('Y-m-d H:i:s', strtotime($to)),
            'reportRangeTitle' => $title
        ]);

        // return view('totals-table', ['totals' => $reporter->totals([date('Y-m-d H:i:s', strtotime($from)), date('Y-m-d H:i:s', strtotime($to))]), 'reporter' => $reporter]);
    }

    /**
     * Handle the get request to the monthly report
     */
    public function getMonthlyReport()
    {
        return $this->getRangeReport(date('Y-m-d', strtotime('first day of this month')), date('Y-m-d'), 'this month');
        // $reporter = new TimeTrackerReporter;
        // return view('report', [
        //     'totals' => $reporter->totals([date('Y-m-d', strtotime('first day of this month')), date('Y-m-d H:i:s')]),
        //     'timeline' => $reporter->timeline([date('Y-m-d', strtotime('first day of this month')), date('Y-m-d H:i:s')]),
        //     'reporter' => $reporter,
        //     'pieRange' => 'monthly',
        //     'rangeStart' => date('Y-m-d', strtotime('first day of this month')),
        //     'rangeEnd' =>  date('Y-m-d H:i:s'),
        //     'reportRangeTitle' => "this month"
        // ]);
    }


        /**
         * Handle the get request to the monthly report
         */
        public function getWeeklyReport()
        {
            return $this->getRangeReport(date('Y-m-d', strtotime('last monday')), date('Y-m-d'), 'this week');
            //
            // $reporter = new TimeTrackerReporter;
            // return view('report', [
            //     'totals' => $reporter->totals([date('Y-m-d', strtotime('first day of this month')), date('Y-m-d H:i:s')]),
            //     'timeline' => $reporter->timeline([date('Y-m-d', strtotime('first day of this month')), date('Y-m-d H:i:s')]),
            //     'reporter' => $reporter,
            //     'pieRange' => 'weekly',
            //     'rangeStart' => date('Y-m-d', strtotime('first day of this month')),
            //     'rangeEnd' =>  date('Y-m-d H:i:s'),
            //     'reportRangeTitle' => "this month"
            // ]);
        }
}
