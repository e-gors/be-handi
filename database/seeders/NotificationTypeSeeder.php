<?php

namespace Database\Seeders;

use App\NotificationType;
use Illuminate\Database\Seeder;

class NotificationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $notificationTypes = ['New Job Posting Email', 'Proposal Email', 'Offer Email', 'Contract Email'];
        foreach ($notificationTypes as $notificationType) {
            $data = NotificationType::where('name', $notificationType)->first();
            if (empty($data)) {
                NotificationType::updateOrCreate([
                    'name' => $notificationType
                ]);
            }
        }
    }
}
