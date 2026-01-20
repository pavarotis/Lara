<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name')->comment('Category name');
            $table->string('slug')->comment('URL-friendly slug');
            $table->text('description')->nullable()->comment('Category description');
            $table->string('color')->nullable()->comment('Category color for display');
            $table->integer('sort_order')->default(0)->comment('Order for display');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['business_id', 'slug']);
            $table->index('business_id');
            $table->index('is_active');
            $table->index('sort_order');
        });

        // Pivot table for content-category relationship
        Schema::create('content_blog_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained()->cascadeOnDelete();
            $table->foreignId('blog_category_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['content_id', 'blog_category_id']);
            $table->index('content_id');
            $table->index('blog_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_blog_category');
        Schema::dropIfExists('blog_categories');
    }
};
