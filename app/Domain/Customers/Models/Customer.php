<?php

declare(strict_types=1);

namespace App\Domain\Customers\Models;

use App\Domain\Catalog\Models\RecurringProfile;
use App\Domain\Orders\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'user_id',
        'customer_group_id',
        'name',
        'email',
        'phone',
        'address',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function recurringProfiles(): HasMany
    {
        return $this->hasMany(RecurringProfile::class);
    }

    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(CustomerApproval::class);
    }

    public function customFields(): BelongsToMany
    {
        return $this->belongsToMany(CustomField::class, 'customer_custom_field')
            ->withPivot('value')
            ->withTimestamps();
    }
}
