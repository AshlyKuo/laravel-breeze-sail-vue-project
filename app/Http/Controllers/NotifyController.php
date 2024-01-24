<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class NotifyController extends Controller
{
    /**
     * 獲得當日打卡上班的時間
     */
    public function getFirst(){
        $day = now()->format('Y-m-d');

        $users = DB::table('users')
        ->whereNotNull('notify_token')
        ->where('punch_in_time', null)
        ->select('name')
        ->get();

        $usersData = [];

        foreach($users as $user){

            $punchInRecord = DB::connection('sqlsrv')
            ->table('onlinemsg')
            ->where('empno', $user->name)
            ->whereDate('recorddatetime', $day)
            ->orderBy('recorddatetime', 'asc') 
            ->first();

            if(!$punchInRecord){
                continue;
            }

            $punchIn = $punchInRecord->recorddatetime;

            $hour = Carbon::parse($punchIn)->hour;
            $minute = Carbon::parse($punchIn)->minute;

            if($hour == 8 || ($hour == 9 && $minute == 0)){
                $timeToPunchOut = Carbon::parse($punchIn)->addHours(9)->addMinutes(20)->toDateTimeString();
            }else{
                $timeToPunchOut = Carbon::parse($punchIn)->setTime(17, 20, 0)->toDateTimeString();
            }

            DB::table('users')
            ->where('name', $user->name)
            ->update(['punch_in_time' => $punchIn, 'expected_punch_out_time' => $timeToPunchOut, 'notified' => 'N']);

            $usersData[] = [
                'name' => $user->name,
                'punch_in_time' => $punchIn,
                'expected_punch_out_time' => $timeToPunchOut,
                'notified'=> 'N'
            ];
        };
        
        if (!empty($usersData)) {
            DB::table('users')
            ->upsert($usersData, ['name'], ['punch_in_time', 'expected_punch_out_time','notified']);
        };
    }

    /**
     * 應下班時間未打卡
     * 透過Line Notify通知
     */
    public function sendNotify(){

        $day = now()->format('Y-m-d');

        $users = DB::table('users')
        ->whereNotNull('notify_token')
        ->where('notified', 'N')
        ->where('expected_punch_out_time', '<', now()->addHours(8)->addMinutes(25))
        ->select('name', 'notify_token', 'expected_punch_out_time')
        ->get();

        foreach($users as $user){
            $lastPunch = DB::connection('sqlsrv')
            ->table('onlinemsg')
            ->where('empno', $user->name)
            ->whereDate('recorddatetime', $day)
            ->orderBy('recorddatetime', 'desc') 
            ->first()
            ->recorddatetime;  

            $timeToPunchOut = $user->expected_punch_out_time;

            $notify_token = $user->notify_token;

            if(Carbon::parse($timeToPunchOut)->isBefore(Carbon::now()->addHours(8)->addMinutes(25)) &&
            Carbon::parse($lastPunch)->isBefore(Carbon::parse($timeToPunchOut))){
                $headers = [
                    'Content-Type: application/x-www-form-urlencoded',
                    'Authorization: Bearer ' . $notify_token
                ];
    
                $message = [
                    'message' => '記得打卡啊啊啊',
                    'stickerPackageId' => '789',
                    'stickerId' => '10886'
                ];
    
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://notify-api.line.me/api/notify");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($message));
                $result = curl_exec($ch);
                curl_close($ch);   

                DB::table('users')
                ->where('name', $user->name )
                ->update(['punch_in_time' => null, 'expected_punch_out_time' => null, 'notified' => null]);


            }else if(Carbon::parse($lastPunch)->isAfter(Carbon::parse($timeToPunchOut))){
                DB::table('users')
                ->where('name', $user->name )
                ->update(['punch_in_time' => null, 'expected_punch_out_time' => null, 'notified' => null]);
            }

        };
    }

    /**
     * 是否為上班日
     */
    public function isHoliday()
    {        
        $currentYear = date('Y');

        $json_url = 'https://cdn.jsdelivr.net/gh/ruyut/TaiwanCalendar/data/'.$currentYear.'.json';
        $json_data = file_get_contents($json_url);
        $data_array = json_decode($json_data, true);

        $jsonString = json_encode($data_array, JSON_PRETTY_PRINT);

        Storage::put('holiday_data.json', $jsonString);
    }

}