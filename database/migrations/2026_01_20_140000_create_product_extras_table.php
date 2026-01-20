<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_extras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('key')->comment('Extra key (e.g., size, color, addon)');
            $table->text('value')->nullable()->comment('Extra value (stored as text, cast based on type)');
            $table->enum('type', ['string', 'number', 'boolean', 'json'])->default('string');
            $table->text('label')->nullable()->comment('Display label for admin/frontend');
            $table->integer('sort_order')->default(0)->comment('Order for display');
            $table->timestamps();

            $table->unique(['product_id', 'key']);
            $table->index('product_id');
            $table->index('key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_extras');
    }
};
