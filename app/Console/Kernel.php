<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\NotifyController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\ChangePasswordToUsername::class,
    ];
    
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        /**
         * 檢查是否為上班日
         */
        $schedule->call (function(){
            $Dashboard = new NotifyController;
            $Dashboard->isHoliday();
        })
        ->yearly();

        /**
         * 獲得上班時間
         */
        $schedule->call(function(){
            $Dashboard = new NotifyController;
            $Dashboard->getFirst();
        })
        ->hourly()
        ->between('8:00','18:00')
        ->skip(function(){
            $dayOfYear = date('z');
            $json_data = Storage::get('holiday_data.json');
            $data_array = json_decode($json_data, true);
            $isHoliday = $data_array[$dayOfYear]['isHoliday'];

            return $isHoliday;
        });

        /**
         * 於應打卡時間一分鐘後
         * 判斷是否需要寄送通知
         */
        $schedule->call(function(){
            $Dashboard = new NotifyController;
            $Dashboard->sendNotify();
        })
        ->everyMinute()
        ->between('17:20', '18:22')
        ->skip(function(){
            $dayOfYear = date('z');
            $json_data = Storage::get('holiday_data.json');
            $data_array = json_decode($json_data, true);
            $isHoliday = $data_array[$dayOfYear]['isHoliday'];

            return $isHoliday;
        });
    
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
