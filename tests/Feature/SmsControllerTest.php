<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Sms;
use App\Enums\SmsStatus;
use App\Repositories\SmsRepository;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SmsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_sent_sms_list(): void
    {
        $sms1 = Sms::factory()->sent()->create(['sent_at' => now()->subHour()]);
        $sms2 = Sms::factory()->sent()->create(['sent_at' => now()]);

        Sms::factory()->pending()->create();
        Sms::factory()->failed()->create();

        $response = $this->getJson('/api/v1/sms');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonPath('0.id', $sms2->id);
        $response->assertJsonPath('1.id', $sms1->id);
    }

    public function test_index_limits_results(): void
    {
        Sms::factory()->sent()->count(3)->create();

        $response = $this->getJson('/api/v1/sms?limit=2');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    public function test_index_handles_invalid_limit(): void
    {
        Sms::factory()->sent()->count(5)->create();

        $response = $this->getJson('/api/v1/sms?limit=0');
        $response->assertStatus(200);
        $response->assertJsonCount(1);

        $response = $this->getJson('/api/v1/sms?limit=300');
        $response->assertStatus(200);
        $response->assertJsonCount(5);
    }

    public function test_store_creates_pending_sms(): void
    {
        $payload = [
            'recipient_phone' => '905551234567',
            'message' => 'Test message'
        ];

        $response = $this->postJson('/api/v1/sms', $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('recipient_phone', '905551234567');
        $response->assertJsonPath('message', 'Test message');
        $response->assertJsonPath('status', 'pending');
        $response->assertJsonPath('provider_message_id', null);
        $response->assertJsonPath('sent_at', null);
        $response->assertJsonPath('error', null);

        $this->assertDatabaseHas('sms', [
            'recipient_phone' => '905551234567',
            'message' => 'Test message',
            'status' => SmsStatus::Pending->value,
        ]);
    }

    public function test_store_validates_phone_number(): void
    {
        $response = $this->postJson('/api/v1/sms', ['message' => 'Test']);
        $response->assertStatus(422);

        $response = $this->postJson('/api/v1/sms', [
            'recipient_phone' => '555123456',
            'message' => 'Test'
        ]);
        $response->assertStatus(422);

        $response = $this->postJson('/api/v1/sms', [
            'recipient_phone' => '905551234567',
            'message' => 'Test'
        ]);
        $response->assertStatus(201);
    }

    public function test_store_validates_message(): void
    {
        $response = $this->postJson('/api/v1/sms', ['recipient_phone' => '905551234567']);
        $response->assertStatus(422);

        $response = $this->postJson('/api/v1/sms', [
            'recipient_phone' => '905551234567',
            'message' => str_repeat('a', 201)
        ]);
        $response->assertStatus(422);

        $response = $this->postJson('/api/v1/sms', [
            'recipient_phone' => '905551234567',
            'message' => 'Valid message'
        ]);
        $response->assertStatus(201);
    }

    public function test_show_returns_existing_sms(): void
    {
        $sms = Sms::factory()->sent()->create();

        $response = $this->getJson("/api/v1/sms/{$sms->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('id', $sms->id);
        $response->assertJsonPath('recipient_phone', $sms->recipient_phone);
        $response->assertJsonPath('message', $sms->message);
        $response->assertJsonPath('status', 'sent');
    }

    public function test_show_returns_404_for_nonexistent_sms(): void
    {
        $response = $this->getJson('/api/v1/sms/999');

        $response->assertStatus(404);
        $response->assertJsonPath('code', 'not_found');
        $response->assertJsonPath('message', 'Not found');
    }
}
