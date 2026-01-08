<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('theme_presets', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // 'cafe', 'restaurant', 'retail'
            $table->string('name'); // 'Cafe', 'Restaurant', 'Retail'
            $table->json('tokens'); // Design tokens JSON
            $table->json('default_modules')->nullable(); // Default modules per region
            $table->string('default_header_variant')->default('minimal');
            $table->string('default_footer_variant')->default('simple');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theme_presets');
    }
};
