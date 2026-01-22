<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
            $table->string('from_url');
            $table->string('to_url');
            $table->enum('type', ['301', '302'])->default('301');
            $table->boolean('is_active')->default(true);
            $table->integer('hits')->default(0);
            $table->timestamp('last_hit_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['business_id', 'from_url']);
            $table->index('business_id');
            $table->index('from_url');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redirects');
    }
};
