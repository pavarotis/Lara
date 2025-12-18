<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->foreignId('layout_id')->nullable()->after('type')->constrained('layouts')->nullOnDelete();
            $table->index('layout_id');
        });
    }

    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropForeign(['layout_id']);
            $table->dropIndex(['layout_id']);
            $table->dropColumn('layout_id');
        });
    }
};
