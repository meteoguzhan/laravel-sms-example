<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ErrorResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            'code' => $this->resource['code'] ?? 'error',
            'message' => $this->resource['message'] ?? 'An error occurred',
            'details' => $this->resource['details'] ?? null,
        ];
    }
}