<?php
namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function index()
    {
//        $data = DB::table('breakdowns')->get();
//        return response()->json($data);

//        function getMonthHoursDaysFromSeconds($seconds)
//        {
//            $secondsInMinute = 60;
//            $secondsInHour = $secondsInMinute * 60;
//            $secondsInDay = $secondsInHour * 24;
//            $secondsInMonth = $secondsInDay * 30;
//
//            $months = floor($seconds / $secondsInMonth);
//
//            $seconds %= $secondsInMonth;
//
//            $days = floor($seconds / $secondsInDay);
//
//            $seconds %= $secondsInDay;
//
//            $hours = $seconds / $secondsInHour;
//
//            $result = [
//                'm' => $months,
//                'd' => $days,
//                'h' => $hours,
//            ];
//
//            return $result;
//        }

//        $seconds = 5142600;
//        $convertedTime = secondsToMonthDaysHours($seconds);
//        print_r($convertedTime);

    }
}
