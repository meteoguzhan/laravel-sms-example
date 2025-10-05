<?php

namespace App\Services\Sms;

readonly class SmsResponse
{
    public function __construct(
        public ?string $message = null,
        public ?string $messageId = null,
        public ?string $error = null,
    ) {
    }

    public static function success(string $messageId, ?string $message = 'Accepted'): self
    {
        return new self(message: $message, messageId: $messageId, error: null);
    }

    public static function error(string $error): self
    {
        return new self(message: null, messageId: null, error: $error);
    }

    public function isSuccess(): bool
    {
        return $this->error === null && $this->messageId !== null;
    }
}
