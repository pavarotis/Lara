<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Models;

use App\Domain\Businesses\Models\Business;
use App\Domain\Customers\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringProfile extends Model
{
    protected $fillable = [
        'business_id',
        'customer_id',
        'product_id',
        'name',
        'frequency',
        'duration',
        'price',
        'status',
        'next_billing_date',
        'last_billing_date',
        'total_cycles',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration' => 'integer',
        'total_cycles' => 'integer',
        'next_billing_date' => 'date',
        'last_billing_date' => 'date',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePaused($query)
    {
        return $query->where('status', 'paused');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isOngoing(): bool
    {
        return $this->duration === null;
    }
}
