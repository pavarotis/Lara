<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_module_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_instance_id')->constrained()->cascadeOnDelete();
            $table->string('region'); // 'header_top', 'content_top', 'main_content', etc.
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['content_id', 'module_instance_id', 'region'], 'unique_assignment');
            $table->index('content_id');
            $table->index('region');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_module_assignments');
    }
};
