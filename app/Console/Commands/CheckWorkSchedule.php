<?php

namespace App\Console\Commands;

use App\User;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class CheckWorkSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:work-schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check work schedule of users and send SMS if needed';

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
        $users = User::all();

        foreach ($users as $user) {
            $workSchedule = $user->workSchedule;

            if ($workSchedule) {
                $now = now();
                $scheduleDate = \Carbon\Carbon::parse($workSchedule->date);

                // Check if the work schedule is tomorrow
                if ($scheduleDate->isTomorrow()) {
                    $this->sendSMS($user, 'Your work schedule is tomorrow.');
                }

                // Check if the work schedule is only one day away
                if ($now->diffInDays($scheduleDate) == 1) {
                    $this->sendSMS($user, 'Your work schedule is only one day away.');
                }
            }
        }
    }

    // protected function sendSMS($user, $message)
    // {
    //     $sid = env('TWILIO_ACCOUNT_SID');
    //     $token = env('TWILIO_AUTH_TOKEN');
    //     $twilioNumber = env('TWILIO_NUMBER');

    //     $client = new Client($sid, $token);

    //     $client->messages->create(
    //         $user->phoneNumber,
    //         [
    //             'from' => $twilioNumber,
    //             'body' => $message
    //         ]
    //     );

    //     $this->info("SMS sent to {$user->name}");
    // }
}
