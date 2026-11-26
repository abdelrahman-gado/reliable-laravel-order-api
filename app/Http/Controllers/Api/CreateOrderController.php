<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateOrderAction;
use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class CreateOrderController extends Controller
{
    /**
     * Handle the incoming request.
     * @throws \Throwable
     */
    public function __invoke(Request $request, CreateOrderAction $createOrderAction)
    {
        $validated = $request->validate([
            'request_id' => ['required', 'string', 'max:64'],
            'customer_email' => ['required', 'email'],
            'total_cents' => ['required', 'integer', 'min:1'],
        ]);

        $order = $createOrderAction->handle($validated);

        return response()->json([
            'created' => $order->wasRecentlyCreated,
            'data' => [
                'id' => $order->id,
                'request_id' => $order->request_id,
                'customer_email' => $order->customer_email,
                'total_cents' => $order->total_cents,
                'status' => $order->status,
            ],
        ], $order->wasRecentlyCreated ? 201 : 200);
    }
}
