<?php

namespace App\Services\Sms;

use App\Enums\ErrorCode;

class SmsErrors
{
    public static function message(ErrorCode $code): string
    {
        return match ($code) {
            ErrorCode::NotFound => 'Not found',
            ErrorCode::ProviderUnexpectedResponse => 'Unexpected response from SMS provider',
            ErrorCode::ProviderRequestFailed => 'SMS provider request failed',
            ErrorCode::ProviderReturnedError => 'SMS provider returned error',
            ErrorCode::UnknownError => 'Unknown error',
        };
    }
}
