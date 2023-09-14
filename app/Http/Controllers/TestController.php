<?php
namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function index()
    {
        $data = DB::table('breakdowns')->get();
        return response()->json($data);
    }
}
