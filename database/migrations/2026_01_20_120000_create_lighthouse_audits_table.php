<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lighthouse_audits', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('device')->default('desktop');
            $table->decimal('performance', 5, 1)->nullable();
            $table->decimal('accessibility', 5, 1)->nullable();
            $table->decimal('best_practices', 5, 1)->nullable();
            $table->decimal('seo', 5, 1)->nullable();
            $table->decimal('pwa', 5, 1)->nullable();
            $table->string('report_path')->nullable();
            $table->timestamp('audited_at')->nullable();
            $table->timestamps();

            $table->index(['url', 'device']);
            $table->index('audited_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lighthouse_audits');
    }
};
