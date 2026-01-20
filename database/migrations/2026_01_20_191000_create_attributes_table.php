<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_group_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->enum('type', ['text', 'number', 'boolean', 'select'])->default('text');
            $table->text('default_value')->nullable();
            $table->json('options')->nullable()->comment('Options for select type');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_required')->default(false);
            $table->timestamps();

            $table->index('attribute_group_id');
            $table->index('is_active');
            $table->unique(['attribute_group_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
