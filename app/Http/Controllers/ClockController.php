<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;


class ClockController extends Controller
{
    protected $connection = 'sqlsrv';
    public function index()
    {
        // phpinfo();exit;
        $data = DB::connection('sqlsrv')
        ->table('onlinemsg')
        ->where('empno', 'A02870')
        ->whereBetween('recorddatetime', ['2023-10-23 08:00:00', '2023-10-23 18:00:00'])
        ->first();
        
        $data2 = DB::table('users')->first();


        print_r($data);
        echo "<br>";
        print_r($data2);
        // $data = DB::connection('sqlsrv')->select('SELECT 1 AS result');
        // dd($data);
    }
}