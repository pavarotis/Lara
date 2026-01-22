<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create geo_zones table first if it doesn't exist
        if (! Schema::hasTable('geo_zones')) {
            Schema::create('geo_zones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
                $table->string('name');
                $table->text('description')->nullable();
                $table->json('zones')->nullable()->comment('Array of zone IDs or country codes');
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('business_id');
                $table->index('is_active');
            });
        }

        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('type')->default('percentage')->comment('percentage or fixed');
            $table->decimal('rate', 8, 4)->default(0)->comment('Tax rate (e.g., 24.0000 for 24%)');
            $table->foreignId('geo_zone_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('priority')->default(1)->comment('Priority order when multiple taxes apply');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('business_id');
            $table->index('geo_zone_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
