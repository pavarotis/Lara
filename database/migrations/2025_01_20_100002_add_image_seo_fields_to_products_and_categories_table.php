<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add image SEO fields to products table
        Schema::table('products', function (Blueprint $table) {
            $table->string('image_alt')->nullable()->after('image');
            $table->string('image_title')->nullable()->after('image_alt');
        });

        // Add image SEO fields to categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->string('image_alt')->nullable()->after('image');
            $table->string('image_title')->nullable()->after('image_alt');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['image_alt', 'image_title']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['image_alt', 'image_title']);
        });
    }
};
