<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sms;
use App\Enums\SmsStatus;

class SmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sadece 10 adet bekleyen SMS oluştur
        foreach (range(1, 10) as $i) {
            Sms::create([
                'recipient_phone' => '905555555' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'message' => 'Demo: Bekleyen mesaj #' . $i,
                'status' => SmsStatus::Pending,
            ]);
        }
    }
}
