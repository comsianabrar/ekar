<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\BreakDown;


class BreakDownController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function calculate(Request $request)
    {
        // Validate input data
        $this->validate($request, [
            'start_timestamp' => 'required|date',
            'end_timestamp' => 'required|date',
            'time_expressions' => 'required|array',
        ]);

        // Parse input data
        $startTimestamp = new \DateTime($request->input('start_timestamp'));
        $endTimestamp = new \DateTime($request->input('end_timestamp'));
        $timeExpressions = $request->input('time_expressions');

        // Calculate breakdown
        $duration = $endTimestamp->getTimestamp() - $startTimestamp->getTimestamp();
        $breakdownResult = [];

        foreach ($timeExpressions as $expression) {
            list($value, $unit) = sscanf($expression, '%d%s');
            $unit = strtolower($unit);

            switch ($unit) {
                case 's':
                    $breakdownResult[$expression] = $duration;
                    break;
                case 'i':
                    $breakdownResult[$expression] = $duration / 60;
                    break;
                case 'h':
                    $breakdownResult[$expression] = $duration / 3600;
                    break;
                case 'd':
                    $breakdownResult[$expression] = $duration / (3600 * 24);
                    break;
                case 'm':
                    $breakdownResult[$expression] = $duration / (3600 * 24 * 30);
                    break;
                default:
                    // Handle unsupported unit
                    $breakdownResult[$expression] = 'Unsupported unit';
                    break;
            }
        }

        // SAve in Database
        $breakdown = new BreakDown([
            'start_timestamp' => $startTimestamp,
            'end_timestamp' => $endTimestamp,
            'time_expressions' => json_encode($timeExpressions),
            'breakdown_result' => json_encode($breakdownResult),
        ]);
        $breakdown->save();

        return response()->json(['message' => 'Done successfully']);
    }

    public function search(Request $request)
    {
        // Validate input
        $this->validate($request, [
            'start_timestamp' => 'required|date',
            'end_timestamp' => 'required|date',
        ]);

        // Parse input dates
        $startTimestamp = new \DateTime($request->input('start_timestamp'));
        $endTimestamp = new \DateTime($request->input('end_timestamp'));

        // Search in the database
        $breakdowns = BreakDown::where('start_timestamp', $startTimestamp)
            ->where('end_timestamp', $endTimestamp)
            ->get();

        return response()->json($breakdowns);
    }
}
