<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string')->comment('string, boolean, json, integer, decimal');
            $table->text('description')->nullable();
            $table->string('group')->default('general')->comment('Settings group (general, email, etc.)');
            $table->timestamps();

            $table->index('group', 'idx_settings_group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
