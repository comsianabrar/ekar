<?php

namespace App\Http\Controllers;

use App\Models\BreakDown;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BreakDownController extends Controller
{
    public function calculateBreakdown(Request $request)
    {
        // Validate the request data
        $this->validate($request, [
            'start_timestamp' => 'required|date',
            'end_timestamp' => 'required|date',
            'time_expressions' => 'required|array',
        ]);

        $startTimestamp = Carbon::parse($request->input('start_timestamp'));
        $endTimestamp = Carbon::parse($request->input('end_timestamp'));
        $timeExpressions = $request->input('time_expressions');

        $durationInSeconds = $endTimestamp->diffInSeconds($startTimestamp);

        $result = $this->_getMonthHoursDaysFromSeconds($durationInSeconds, $this->_isExpressionExists($timeExpressions, 'm'), $this->_isExpressionExists($timeExpressions, 'd'), $this->_isExpressionExists($timeExpressions, 'h'));

        $breakdown_result = [];

        foreach ($timeExpressions as $expression){
            $value = 1;
            if (preg_match('/^(\d*)([a-zA-Z]+)$/', $expression, $matches)) {
                $value = $matches[1] !== '' ? $matches[1] : 1; // Default to 1 if no number is present
                $unit = $matches[2];
            }
            switch ($unit){

                case 'm':
                    if((int)$result[$unit] == (int)$value){
                        $breakdown_result[$expression] = 1;
                        break;
                    }elseif($result[$unit] < $value){
                        $breakdown_result[$expression] = 0;

                    }else{
                        $breakdown_result[$expression] =  round($result[$unit] / $value, 2);
                        $result['m'] = $result[$unit]%$value;
                    }
                    break;
                    case 'd':
                        if($result[$unit] == $value){
                            $breakdown[$expression] = 1;

                        }elseif($result[$unit] < $value){

                            $breakdown_result[$expression] = 0;

                        }else{
                            $breakdown_result[$expression] =  round($result[$unit] / $value, 2);
                            $result['d'] = $result[$unit]%$value;
                        }
                    break;
                    case 'h':

                        if($result[$unit] == $value){
                            $breakdown[$expression] = 1;
                        }elseif($result[$unit] < $value){
                            $breakdown_result[$expression] = 0;
                        }else{
                            $breakdown_result[$expression] =  round($result[$unit] / $value, 2);
                            $result['h'] = $result[$unit]%$value;
                        }
                    break;
            }

        }

        $breakdownData = [
            'start_timestamp' => $startTimestamp,
            'end_timestamp' => $endTimestamp,
            'time_expressions'=> json_encode($timeExpressions),
            'breakdown_result' => json_encode($breakdown_result),
        ];

        $createdBreakdown = BreakDown::create($breakdownData);

        return response()->json([
            'breakdown' => $breakdown_result,
            'message' => 'Breakdown calculated and stored successfully.',
        ]);
    }

    public function searchBreakdowns(Request $request)
    {
        // Validate the request data
        $this->validate($request, [
            'start_timestamp' => 'required|date',
            'end_timestamp' => 'required|date',
        ]);

        $startTimestamp = Carbon::parse($request->input('start_timestamp'));
        $endTimestamp = Carbon::parse($request->input('end_timestamp'));

        // Search for stored breakdowns
        $breakdowns = BreakDown::where('start_timestamp', $startTimestamp)
            ->where('end_timestamp', $endTimestamp)
            ->get();

        return response()->json([
            'breakdowns' => $breakdowns,
        ]);
    }


    private  function _getMonthHoursDaysFromSeconds($seconds, $includeMonth,$includeDays, $includeHours )
        {
            $secondsPerMinute = 60;
            $secondsPerHour = $secondsPerMinute * 60;
            $secondsPerDay = $secondsPerHour * 24;
            $secondsPerMonth = $secondsPerDay * 30;

            $result = array();

            if ($includeMonth) {
                // Calculate months
                $months = floor($seconds / $secondsPerMonth);
                $seconds -= $months * $secondsPerMonth;
                $result['m'] = $months;
            }

            if ($includeDays) {
                // Calculate days
                $days = floor($seconds / $secondsPerDay);
                $seconds -= $days * $secondsPerDay;
                $result['d'] = $days;
            }

            if ($includeHours) {
                // Calculate hours
                $hours = floor($seconds / $secondsPerHour);
                $result['h'] = $hours;
            }

            return $result;
        }

        private function _isExpressionExists($timeExpressions, $expressionToCheck = 'm'){
            foreach ($timeExpressions as $expression) {
                if (strpos($expression, $expressionToCheck) !== false) {
                  return true;
                }
            }
        }
}
