<?php

namespace App\Enums;

enum ErrorCode: string
{
    case NotFound = 'not_found';
    case ProviderUnexpectedResponse = 'provider_unexpected_response';
    case ProviderRequestFailed = 'provider_request_failed';
    case ProviderReturnedError = 'provider_returned_error';
    case UnknownError = 'unknown_error';
}
