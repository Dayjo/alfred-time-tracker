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

        $totals = $reporter->totals('length');
        $currentlyTracking = $reporter->currentlyTracking();

        return view('dashboard')->with([
            'reporter' => $reporter,
            'currentlyTracking' => $currentlyTracking,
            'today' => $today,
            'totals' => $totals
        ]);
    }
}
