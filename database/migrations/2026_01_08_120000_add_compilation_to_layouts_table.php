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
        if (! Schema::hasTable('layouts')) {
            return;
        }

        Schema::table('layouts', function (Blueprint $table) {
            if (! Schema::hasColumn('layouts', 'compiled_html')) {
                $table->text('compiled_html')->nullable()->after('regions');
            }
            if (! Schema::hasColumn('layouts', 'assets_manifest')) {
                $table->json('assets_manifest')->nullable()->after('compiled_html');
            }
            if (! Schema::hasColumn('layouts', 'critical_css')) {
                $table->text('critical_css')->nullable()->after('assets_manifest');
            }
            if (! Schema::hasColumn('layouts', 'compiled_at')) {
                $table->timestamp('compiled_at')->nullable()->after('critical_css');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('layouts')) {
            return;
        }

        Schema::table('layouts', function (Blueprint $table) {
            $columns = array_filter(
                ['compiled_html', 'assets_manifest', 'critical_css', 'compiled_at'],
                fn ($column) => Schema::hasColumn('layouts', $column)
            );

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
