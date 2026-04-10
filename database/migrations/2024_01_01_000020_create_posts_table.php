<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->enum('type', ['game', 'software', 'apk', 'blog', 'tutorial', 'listicle', 'review'])->default('game');
            $table->enum('status', ['draft', 'pending', 'published', 'scheduled'])->default('draft');
            $table->string('featured_image')->nullable();
            $table->string('version')->nullable();
            $table->string('developer')->nullable();
            $table->string('file_size')->nullable();
            $table->enum('platform', ['pc', 'android', 'ios', 'mac', 'all'])->default('pc');
            $table->date('release_date')->nullable();
            $table->date('updated_date')->nullable();
            $table->text('system_requirements')->nullable();
            $table->text('features')->nullable();
            $table->text('whats_new')->nullable();
            $table->text('pros')->nullable();
            $table->text('cons')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('robots')->default('index, follow');
            $table->enum('schema_type', ['SoftwareApplication', 'Article', 'Review', 'HowTo', 'ItemList'])->default('SoftwareApplication');
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedBigInteger('downloads')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
