<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bisindo_materials') && !Schema::hasColumn('bisindo_materials', 'video_url')) {
            Schema::table('bisindo_materials', function (Blueprint $table) {
                $table->string('video_url')->nullable()->after('gambar_url');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bisindo_materials') && Schema::hasColumn('bisindo_materials', 'video_url')) {
            Schema::table('bisindo_materials', function (Blueprint $table) {
                $table->dropColumn('video_url');
            });
        }
    }
};

