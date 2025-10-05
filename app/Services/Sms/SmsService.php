<?php

namespace App\Services\Sms;

use App\Models\Sms;
use App\Enums\ErrorCode;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Throwable;

class SmsService implements SmsServiceInterface
{
    private Client $client;

    public function __construct(private readonly string $endpoint, ?Client $client = null)
    {
        $this->client = $client ?? new Client([
            'base_uri' => rtrim($this->endpoint, '/') . '/',
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Send the given SMS via provider and return only response payload.
     */
    public function send(Sms $sms): SmsResponse
    {
        try {
            $response = $this->client->post('sms', [
                'json' => [
                    'recipient_phone' => $sms->recipient_phone,
                    'message' => $sms->message,
                ],
                'timeout' => 10,
                'http_errors' => false,
            ]);

            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();
            $data = json_decode($body, true);

            if ($statusCode >= 200 && $statusCode < 300) {
                if (is_array($data) && isset($data['messageId'])) {
                    return SmsResponse::success(
                        messageId: $data['messageId'],
                        message: $data['message'] ?? 'Accepted'
                    );
                }

                $errorText = is_array($data)
                    ? ($data['error'] ?? SmsErrors::message(ErrorCode::ProviderUnexpectedResponse))
                    : SmsErrors::message(ErrorCode::ProviderUnexpectedResponse);
                return SmsResponse::error($errorText);
            }

            if (is_array($data)) {
                $error = $data['error'] ?? null;
                if ($error) {
                    $err = is_string($error) ? $error : json_encode($error);
                    return SmsResponse::error($err ?: SmsErrors::message(ErrorCode::ProviderReturnedError));
                }
            }

            return SmsResponse::error($body ?: SmsErrors::message(ErrorCode::ProviderRequestFailed));
        } catch (GuzzleException|Throwable $e) {
            return SmsResponse::error($e->getMessage() ?: SmsErrors::message(ErrorCode::UnknownError));
        }
    }
}
