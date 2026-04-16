<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('banner_image')->nullable()->after('featured_image');
            $table->json('gallery_images')->nullable()->after('banner_image');
            $table->string('game_language')->nullable()->after('file_size');
            $table->string('focus_keyword')->nullable()->after('meta_keywords');
            $table->string('sys_req_os')->nullable()->after('system_requirements');
            $table->string('sys_req_cpu')->nullable()->after('sys_req_os');
            $table->string('sys_req_gpu')->nullable()->after('sys_req_cpu');
            $table->string('sys_req_ram')->nullable()->after('sys_req_gpu');
            $table->string('sys_req_storage')->nullable()->after('sys_req_ram');
            $table->string('sys_req_software')->nullable()->after('sys_req_storage');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'banner_image', 'gallery_images', 'game_language', 'focus_keyword',
                'sys_req_os', 'sys_req_cpu', 'sys_req_gpu',
                'sys_req_ram', 'sys_req_storage', 'sys_req_software',
            ]);
        });
    }
};
