<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filter_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filter_group_id')->constrained()->cascadeOnDelete();
            $table->string('value');
            $table->string('slug')->nullable();
            $table->string('color')->nullable()->comment('Optional color for display (e.g., #FF0000)');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('filter_group_id');
            $table->index('is_active');
            $table->unique(['filter_group_id', 'value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filter_values');
    }
};
