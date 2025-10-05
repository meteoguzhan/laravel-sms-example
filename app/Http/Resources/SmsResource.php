<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SmsResource extends JsonResource
{
    /**
     * Remove the default 'data' wrapper.
     */
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'recipient_phone' => $this->recipient_phone,
            'message' => $this->message,
            'status' => $this->status instanceof \BackedEnum ? $this->status->value : $this->status,
            'provider_message_id' => $this->provider_message_id,
            'sent_at' => $this->sent_at?->toIso8601String(),
            'error' => $this->error,
        ];
    }
}