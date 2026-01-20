<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained()->cascadeOnDelete();
            $table->string('author_name')->comment('Comment author name');
            $table->string('author_email')->comment('Comment author email');
            $table->text('body')->comment('Comment body/content');
            $table->enum('status', ['pending', 'approved', 'spam', 'rejected'])->default('pending');
            $table->foreignId('parent_id')->nullable()->constrained('blog_comments')->nullOnDelete()->comment('Parent comment for replies');
            $table->string('ip_address')->nullable()->comment('Author IP address for moderation');
            $table->string('user_agent')->nullable()->comment('User agent for moderation');
            $table->timestamps();

            $table->index('content_id');
            $table->index('status');
            $table->index('parent_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_comments');
    }
};
