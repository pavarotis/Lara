<?php

declare(strict_types=1);

namespace App\Domain\Sales\Models;

use App\Domain\Businesses\Models\Business;
use App\Domain\Customers\Models\Customer;
use App\Domain\Orders\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReturn extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'returns';

    protected $fillable = [
        'business_id',
        'order_id',
        'customer_id',
        'return_number',
        'reason',
        'description',
        'status',
        'return_date',
        'processed_date',
        'admin_notes',
    ];

    protected $casts = [
        'return_date' => 'date',
        'processed_date' => 'date',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
