<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CreateOrderController;

Route::post('orders', CreateOrderController::class);
