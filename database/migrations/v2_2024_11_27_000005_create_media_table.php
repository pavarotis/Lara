<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('folder_id')->nullable()->constrained('media_folders')->nullOnDelete();
            $table->string('name');
            $table->string('path'); // Storage path
            $table->json('variants')->nullable(); // {webp: '', avif: '', sizes: {...}}
            $table->string('type'); // 'image', 'video', 'document', etc.
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->nullable(); // bytes
            $table->json('metadata')->nullable(); // width, height, duration, etc.
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('business_id');
            $table->index('folder_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
