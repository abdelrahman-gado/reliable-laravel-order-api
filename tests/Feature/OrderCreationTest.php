<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\OrderStatusEnum;
use App\Jobs\ProcessOrderJob;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Queue;
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

    #[Test]
    public function test_repeated_request_is_idempotent_and_dispatches_one_job(): void
    {

        Queue::fake();
        $payload = [
            'request_id' => 'checkout-2026-0001',
            'customer_email' => 'developer@example.com',
            'total_cents' => 2500,
        ];

        $firstResponse = $this->postJson('/api/orders', $payload);
        $retryResponse = $this->postJson('/api/orders', $payload);

        $firstResponse->assertCreated()
            ->assertJsonPath('created', true)
            ->assertJsonPath('data.status', OrderStatusEnum::PENDING->value);

        $retryResponse->assertOk()
            ->assertJsonPath('created', false);

        $this->assertSame(
            $firstResponse['data']['id'],
            $retryResponse['data']['id']
        );

        $this->assertDatabaseCount('orders', 1);
        Queue::assertPushed(ProcessOrderJob::class, 1);
    }

    #[Test]
    public function test_order_input_is_required(): void
    {
        Queue::fake();
        $this->postJson('/api/orders', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'request_id',
                'customer_email',
                'total_cents',
            ]);

        Queue::assertNothingPushed();
    }
}
