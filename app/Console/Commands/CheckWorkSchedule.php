<?php

namespace App\Console\Commands;

use App\User;
use App\Contract;
use App\Mail\ScheduleReminder;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckWorkSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:check';
    protected $description = 'Check for scheduled tasks and send notifications to users';

    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tasks = Contract::whereDate('start_date', Carbon::tomorrow())->get();

        foreach ($tasks as $task) {
            $user = User::find($task->user_id);

            // Send email notification
            Mail::to($user->email)->send(new ScheduleReminder($task));

            // Update user status
            $user->update(['status' => 'unavailable']);
        }
    }
}
