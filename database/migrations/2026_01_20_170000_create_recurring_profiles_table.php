<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'yearly'])->default('monthly');
            $table->integer('duration')->nullable()->comment('Number of cycles (null = ongoing)');
            $table->decimal('price', 10, 2);
            $table->enum('status', ['active', 'paused', 'cancelled', 'completed'])->default('active');
            $table->date('next_billing_date')->nullable();
            $table->date('last_billing_date')->nullable();
            $table->integer('total_cycles')->default(0)->comment('Number of times billed');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('business_id');
            $table->index('customer_id');
            $table->index('product_id');
            $table->index('status');
            $table->index('next_billing_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_profiles');
    }
};
