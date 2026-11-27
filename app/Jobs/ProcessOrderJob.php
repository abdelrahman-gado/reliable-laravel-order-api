<?php declare(strict_types=1);

namespace App\Jobs;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\Middleware\WithoutOverlapping;

#[Tries(3)]
class ProcessOrderJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order)
    {
    }

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping((string) $this->order->id))
                ->releaseAfter(5)
                ->expireAfter(30),
        ];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
//        sleep(3); // just for debugging and showing
        $this->order->update([
            'status' => OrderStatusEnum::PROCESSED
        ]);
    }
}
