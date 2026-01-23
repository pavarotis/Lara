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
            $table->unsignedBigInteger('content_id');
            $table->string('author_name')->comment('Comment author name');
            $table->string('author_email')->comment('Comment author email');
            $table->text('body')->comment('Comment body/content');
            $table->enum('status', ['pending', 'approved', 'spam', 'rejected'])->default('pending');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('Parent comment for replies');
            $table->string('ip_address')->nullable()->comment('Author IP address for moderation');
            $table->string('user_agent')->nullable()->comment('User agent for moderation');
            $table->timestamps();

            $table->index('content_id');
            $table->index('status');
            $table->index('parent_id');
            $table->index('created_at');
        });

        // Add foreign keys after table creation
        Schema::table('blog_comments', function (Blueprint $table) {
            $table->foreign('content_id')
                ->references('id')
                ->on('contents')
                ->onDelete('cascade');

            $table->foreign('parent_id')
                ->references('id')
                ->on('blog_comments')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('blog_comments', function (Blueprint $table) {
            $table->dropForeign(['content_id']);
            $table->dropForeign(['parent_id']);
        });

        Schema::dropIfExists('blog_comments');
    }
};
