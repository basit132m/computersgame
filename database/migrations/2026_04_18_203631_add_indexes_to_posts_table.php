<?php

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
        Schema::table('posts', function (Blueprint $table) {
            // Composite index used by scopePublished() on every page load
            $table->index(['status', 'published_at'], 'posts_status_published_at_idx');
            // Used by ofType() scope and homepage section filters
            $table->index('type', 'posts_type_idx');
            // Used in category pages and joins
            $table->index('category_id', 'posts_category_id_idx');
            // Used for sorting by popularity
            $table->index('downloads', 'posts_downloads_idx');
            $table->index('views', 'posts_views_idx');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_status_published_at_idx');
            $table->dropIndex('posts_type_idx');
            $table->dropIndex('posts_category_id_idx');
            $table->dropIndex('posts_downloads_idx');
            $table->dropIndex('posts_views_idx');
        });
    }
};
