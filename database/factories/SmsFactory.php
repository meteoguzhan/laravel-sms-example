<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Sms;
use App\Enums\SmsStatus;

/**
 * @extends Factory<Sms>
 */
class SmsFactory extends Factory
{
    protected $model = Sms::class;

    public function definition(): array
    {
        return [
            'recipient_phone' => '90' . $this->faker->numberBetween(5000000000, 5999999999),
            'message' => $this->faker->text(50),
            'status' => SmsStatus::Pending,
            'provider_message_id' => null,
            'sent_at' => null,
            'error' => null,
        ];
    }

    public function pending(): self
    {
        return $this->state(fn () => [
            'status' => SmsStatus::Pending,
            'provider_message_id' => null,
            'sent_at' => null,
            'error' => null,
        ]);
    }

    public function processing(): self
    {
        return $this->state(fn () => [
            'status' => SmsStatus::Processing,
            'provider_message_id' => null,
            'sent_at' => null,
            'error' => null,
        ]);
    }

    public function sent(): self
    {
        return $this->state(fn () => [
            'status' => SmsStatus::Sent,
            'provider_message_id' => 'msg_' . $this->faker->uuid(),
            'sent_at' => now(),
            'error' => null,
        ]);
    }

    public function failed(): self
    {
        return $this->state(fn () => [
            'status' => SmsStatus::Failed,
            'provider_message_id' => null,
            'sent_at' => null,
            'error' => 'Provider error',
        ]);
    }
}