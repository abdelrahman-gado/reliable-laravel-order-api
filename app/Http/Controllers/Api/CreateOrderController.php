<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class CreateOrderController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'request_id' => ['required', 'string', 'max:64'],
            'customer_email' => ['required', 'email'],
            'total_cents' => ['required', 'integer', 'min:1'],
        ]);

        $order = Order::create([
            'request_id' => $validated['request_id'],
            'customer_email' => $validated['customer_email'],
            'total_cents' => $validated['total_cents'],
            'status' => OrderStatusEnum::PENDING,
        ]);

        return response()->json([
            'data' => [
                'id' => $order->id,
                'request_id' => $order->request_id,
                'customer_email' => $order->customer_email,
                'total_cents' => $order->total_cents,
                'status' => $order->status,
            ],
        ], 201);
    }
}
