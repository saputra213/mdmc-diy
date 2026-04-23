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
        if (!Schema::hasColumn('lokasis', 'latitude')) {
            Schema::table('lokasis', function (Blueprint $table) {
                $table->string('latitude')->nullable()->after('nama_lokasi');
                $table->string('longitude')->nullable()->after('latitude');
            });
        }

        if (!Schema::hasColumn('bantuan_requests', 'latitude')) {
            Schema::table('bantuan_requests', function (Blueprint $table) {
                $table->string('latitude')->nullable()->after('lokasi_detail');
                $table->string('longitude')->nullable()->after('latitude');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lokasis', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });

        Schema::table('bantuan_requests', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
