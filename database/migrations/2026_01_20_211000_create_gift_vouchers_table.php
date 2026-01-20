<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gift_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('voucher_theme_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code')->unique();
            $table->string('from_name')->nullable();
            $table->string('from_email')->nullable();
            $table->string('to_name')->nullable();
            $table->string('to_email')->nullable();
            $table->text('message')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'active', 'used', 'expired', 'cancelled'])->default('pending');
            $table->date('expiry_date')->nullable();
            $table->date('used_date')->nullable();
            $table->decimal('balance', 10, 2)->default(0);
            $table->timestamps();

            $table->index('business_id');
            $table->index('voucher_theme_id');
            $table->index('order_id');
            $table->index('code');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_vouchers');
    }
};
