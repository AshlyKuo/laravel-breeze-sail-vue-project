<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ChangePasswordToUsername extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:change-password {username}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        
        $username = $this->argument('username');
        $user = User::where('name', $username)->first();

        if (!$user) {
            $this->error('User not found!');
            return;
        }
        
        $user->password = Hash::make($user->name.$user->name);
        $user->save();

        $this->info("{$username}'s password has been changed to their username.");
    }
}
