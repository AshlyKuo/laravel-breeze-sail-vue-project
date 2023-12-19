<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class DataController extends Controller
{
    
    /**
    * 獲得$day的第一筆和最後一筆刷卡資料
    */
    public function index($day){
        $user = Auth::user();
        
        $punchData = DB::connection('sqlsrv')
        ->table('onlinemsg')
        ->where('empno', $user->name)
        ->whereDate('recorddatetime', $day)
        ->selectRaw('MIN(recorddatetime) as punchIn, MAX(recorddatetime) as punchOut')
        ->first();
        
        $data = [
            'punchIn' => $punchData->punchIn,
            'punchOut' => $punchData->punchOut
        ];

        return response()->json($data);
    }

}
