<?php declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessOrderJob;

class CreateOrderAction
{
    /**
     * @param array{request_id: string, customer_email: string, total_cents: int} $data
     * @return Order
     * @throws \Throwable
     */
    public function handle(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order =  Order::firstOrCreate(
                ['request_id' => $data['request_id']],
                [
                    'customer_email' => $data['customer_email'],
                    'total_cents' => $data['total_cents'],
                    'status' => OrderStatusEnum::PENDING
                ]
            );

            if ($order->wasRecentlyCreated) {
                ProcessOrderJob::dispatch($order)->afterCommit();
            }

            return $order;
        }, attempts: 3);
    }
}
