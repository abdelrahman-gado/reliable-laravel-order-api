<?php declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderCreationTest extends TestCase
{
    use DatabaseMigrations;

    #[Test]
    public function test_repeated_request_creates_only_one_order(): void
    {
        $payload = [
            'request_id' => 'checkout-2026-0001',
            'customer_email' => 'developer@example.com',
            'total_cents' => 2500,
        ];

        $firstResponse = $this->postJson('/api/orders', $payload);
        $retryResponse = $this->postJson('/api/orders', $payload);

        $firstResponse->assertCreated()->assertJsonPath('created', true);
        $retryResponse->assertOk()->assertJsonPath('created', false);
        $this->assertSame($firstResponse['data']['id'], $retryResponse['data']['id']);

        $this->assertDatabaseCount('orders', 1);
    }
}
