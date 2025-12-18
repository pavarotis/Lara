<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // 'Default', 'Full Width', 'Landing'
            $table->string('type')->default('default'); // 'default', 'full-width', 'landing'
            $table->json('regions'); // ['header_top', 'content_top', 'main_content', ...]
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index('business_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layouts');
    }
};
