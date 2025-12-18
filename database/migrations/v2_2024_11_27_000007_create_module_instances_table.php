<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // 'hero', 'rich_text', 'image', 'menu', 'gallery', etc.
            $table->string('name')->nullable(); // For reusable instances
            $table->json('settings'); // Module-specific settings
            $table->json('style')->nullable(); // Background, padding, etc.
            $table->enum('width_mode', ['contained', 'full', 'full-bg-contained-content'])->default('contained');
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->index('business_id');
            $table->index('type');
            $table->index('enabled');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_instances');
    }
};
