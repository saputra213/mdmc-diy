<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('disaster_events', function (Blueprint $table) {
            if (!Schema::hasColumn('disaster_events', 'affected_houses')) {
                $table->unsignedInteger('affected_houses')->nullable()->after('location');
            }
        });
    }

    public function down(): void
    {
        Schema::table('disaster_events', function (Blueprint $table) {
            if (Schema::hasColumn('disaster_events', 'affected_houses')) {
                $table->dropColumn('affected_houses');
            }
        });
    }
};

