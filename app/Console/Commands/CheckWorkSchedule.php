<?php

namespace App\Console\Commands;

use App\User;
use Exception;
use App\Contract;
use GuzzleHttp\Client;
use App\Mail\ScheduleReminder;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
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

    private $sms_apikey;
    private $sms_base_url;
    private $sms_sender_name;


    public function __construct()
    {
        parent::__construct();
        $this->sms_apikey = env('SMS_API_KEY');
        $this->sms_base_url = env('SMS_BASE_URL');
        $this->sms_sender_name = env('SMS_SENDER_NAME');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Log a message indicating the cron job execution
        Log::info('Cron job executed successfully.');
        // $contracts = Contract::whereDate('start_date', Carbon::tomorrow())->get();

        // foreach ($contracts as $contract) {
        //     $user = null;

        //     if ($contract->proposal) {
        //         // If the contract is associated with a proposal
        //         $user = $contract->proposal->user;
        //     } elseif ($contract->offer) {
        //         // If the contract is associated with an offer
        //         $user = $contract->offer->profile->user;
        //     }

        //     if ($user) {
        //         // Send SMS notification
        //         $message = "Your contract with ID: " . $contract->id . " will start tomorrow. Contact your client with this number";
        //         // $this->sendSMSScheduleNotification($user[0]['contact_number'], $message);
        //         Log::info($user);

        //         // Update user status
        //         // $user->profile->update(['status' => 'unavailable']);
        //     }
        // }
    }

    private function sendSMSScheduleNotification($phoneNumber, $message)
    {
        try {
            $params = array(
                'apikey' => $this->sms_apikey,
                'number' => $phoneNumber,
                'message' => $message,
                'sendername' => $this->sms_sender_name
            );

            $client = new Client(['verify' => true]);
            $response = $client->request('POST', $this->sms_base_url, [
                'form_params' => $params
            ]);

            if (!$response) {
                return abort(422, 'Failed to send SMS!');
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return $e->getMessage();
        }
    }
}
