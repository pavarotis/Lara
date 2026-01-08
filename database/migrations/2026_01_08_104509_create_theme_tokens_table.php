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
        Schema::create('theme_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('preset_slug'); // Reference to theme_presets
            $table->json('token_overrides')->nullable(); // Partial token overrides
            $table->string('header_variant')->default('minimal');
            $table->string('footer_variant')->default('simple');
            $table->timestamps();

            $table->index('business_id');
            $table->index('preset_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theme_tokens');
    }
};
