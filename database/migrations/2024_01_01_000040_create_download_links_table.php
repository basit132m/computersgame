<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('download_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->string('label');
            $table->string('url');
            $table->string('platform')->nullable();
            $table->string('file_size')->nullable();
            $table->string('version')->nullable();
            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('clicks')->default(0);
            $table->timestamps();

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });

        Schema::create('download_clicks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('link_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('referer')->nullable();
            $table->enum('source', ['article_button', 'timer_page'])->default('article_button');
            $table->timestamp('clicked_at')->useCurrent();

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('link_id')->references('id')->on('download_links')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('download_clicks');
        Schema::dropIfExists('download_links');
    }
};
