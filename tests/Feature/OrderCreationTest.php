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

        $this->postJson('/api/orders', $payload)->assertCreated();
        $this->postJson('/api/orders', $payload)->assertCreated();

        $this->assertDatabaseCount('orders', 1);
    }
}
