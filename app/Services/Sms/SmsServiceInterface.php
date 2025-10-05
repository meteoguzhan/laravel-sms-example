<?php

namespace App\Services\Sms;
use App\Models\Sms;

interface SmsServiceInterface
{
    public function send(Sms $sms): SmsResponse;
}
