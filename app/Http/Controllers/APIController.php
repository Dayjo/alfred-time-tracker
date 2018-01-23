<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Services\TimeTracker\TimeTrackerReporter;

class APIController extends Controller
{
    public function __construct()
    {
        $this->reporter =   new TimeTrackerReporter;
    }

    /**
     * Handle api requests to Get the currently tracked task
     * @return JsonResponse
     */
    public function getCurrentlyTracking()
    {
        $currentlyTracking = $this->reporter->currentlyTracking();

        return $this->apiResponse(array($currentlyTracking));
    }

    /**
     * Handle api call to get weekly totals
     * @return JsonResponse
     */
    public function getTotals($range = 'weekly')
    {
        // Check if it's a custom range
        if (strpos($range, ",") !== false) {
            $range = explode(",", $range);
        }
        
        return $this->apiResponse($this->reporter->totals($range));
    }

    /**
     * Return an api response.
     *
     * @param array $data
     * @param bool $status
     * @param int $header
     * @return \Illuminate\Http\JsonResponse
     */
    protected function apiResponse(array $data = [], bool $status = true, $header = 200): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'data' => $this->prepareData($data),
        ], $header);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareData(array $data): array
    {
        $prepared = [];

        foreach ($data as $name => $value) {
            if (is_object($value)) {
                if ($value instanceof Arrayable) {
                    $value = $value->toArray();
                } elseif (method_exists($value, '__toString')) {
                    $value->__toString();
                }
            }
            $prepared[$name] = $value;
        }

        return $prepared;
    }
}
