<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('layouts', function (Blueprint $table) {
            $table->text('compiled_html')->nullable()->after('regions');
            $table->json('assets_manifest')->nullable()->after('compiled_html');
            $table->text('critical_css')->nullable()->after('assets_manifest');
            $table->timestamp('compiled_at')->nullable()->after('critical_css');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('layouts', function (Blueprint $table) {
            $table->dropColumn(['compiled_html', 'assets_manifest', 'critical_css', 'compiled_at']);
        });
    }
};
