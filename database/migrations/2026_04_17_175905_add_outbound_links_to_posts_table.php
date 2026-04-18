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
            $table->string('developer_url')->nullable()->after('developer');
            $table->string('publisher_url')->nullable()->after('publisher');
            $table->string('category_url')->nullable()->after('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['developer_url', 'publisher_url', 'category_url']);
        });
    }
};
