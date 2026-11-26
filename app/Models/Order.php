<?php declare(strict_types=1);

namespace App\Models;

use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'request_id',
    'customer_email',
    'total_cents',
    'status'
])]
class Order extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => OrderStatusEnum::class,
    ];
}
