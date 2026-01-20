<?php

declare(strict_types=1);

namespace App\Domain\Sales\Models;

use App\Domain\Businesses\Models\Business;
use App\Domain\Orders\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftVoucher extends Model
{
    protected $fillable = [
        'business_id',
        'voucher_theme_id',
        'order_id',
        'code',
        'from_name',
        'from_email',
        'to_name',
        'to_email',
        'message',
        'amount',
        'status',
        'expiry_date',
        'used_date',
        'balance',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'expiry_date' => 'date',
        'used_date' => 'date',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function voucherTheme(): BelongsTo
    {
        return $this->belongsTo(VoucherTheme::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}
