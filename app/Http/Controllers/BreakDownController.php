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

       $breakdown_result = [];

       foreach ($timeExpressions as $expression){
           $expression_formatted = strlen($expression) == 1 ? "1".$expression : $expression;
           preg_match('/(\d+)([mdh])/', $expression_formatted, $matches);

           $value = (int)$matches[1];
           $unit_type = $matches[2];

           $units = 0;

           switch ($unit_type){
               case 'm':
                   $units = floor($durationInSeconds / (30 * 24 * 3600 * $value));
                   $durationInSeconds = $durationInSeconds % (30 * 24 * 3600 * $value);
//                    echo $durationInSeconds. '-';
                   break;
               case 'd':
                   $units = floor($durationInSeconds / (24 * 3600 * $value));
                   $durationInSeconds =  $durationInSeconds % (24 * 3600 * $value);
                   break;
               case 'h':
                    $units = floatval($durationInSeconds / (3600 * $value));
                    $durationInSeconds = $durationInSeconds % (3600 * $value);
                   break;
               default:
                   break;
           }

           $breakdown_result[$expression] = $units;
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
}

