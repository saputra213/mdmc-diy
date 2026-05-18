<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $defaultEventId = null;
        if (Schema::hasTable('disaster_events')) {
            $defaultEventId = DB::table('disaster_events')->insertGetId([
                'name' => 'Migrasi Data',
                'location' => null,
                'status' => 'archived',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (Schema::hasTable('barangs') && !Schema::hasColumn('barangs', 'disaster_event_id')) {
            Schema::table('barangs', function (Blueprint $table) {
                $table->foreignId('disaster_event_id')->nullable()->after('id')->constrained('disaster_events')->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('barang_masuks') && !Schema::hasColumn('barang_masuks', 'disaster_event_id')) {
            Schema::table('barang_masuks', function (Blueprint $table) {
                $table->foreignId('disaster_event_id')->nullable()->after('id')->constrained('disaster_events')->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('barang_keluars') && !Schema::hasColumn('barang_keluars', 'disaster_event_id')) {
            Schema::table('barang_keluars', function (Blueprint $table) {
                $table->foreignId('disaster_event_id')->nullable()->after('id')->constrained('disaster_events')->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('kebutuhans') && !Schema::hasColumn('kebutuhans', 'disaster_event_id')) {
            Schema::table('kebutuhans', function (Blueprint $table) {
                $table->foreignId('disaster_event_id')->nullable()->after('id')->constrained('disaster_events')->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('bantuan_requests') && !Schema::hasColumn('bantuan_requests', 'disaster_event_id')) {
            Schema::table('bantuan_requests', function (Blueprint $table) {
                $table->foreignId('disaster_event_id')->nullable()->after('id')->constrained('disaster_events')->cascadeOnDelete();
            });
        }

        if ($defaultEventId) {
            foreach (['barangs', 'barang_masuks', 'barang_keluars', 'kebutuhans', 'bantuan_requests'] as $table) {
                if (Schema::hasTable($table) && Schema::hasColumn($table, 'disaster_event_id')) {
                    DB::table($table)->whereNull('disaster_event_id')->update(['disaster_event_id' => $defaultEventId]);
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bantuan_requests') && Schema::hasColumn('bantuan_requests', 'disaster_event_id')) {
            Schema::table('bantuan_requests', function (Blueprint $table) {
                $table->dropConstrainedForeignId('disaster_event_id');
            });
        }

        if (Schema::hasTable('kebutuhans') && Schema::hasColumn('kebutuhans', 'disaster_event_id')) {
            Schema::table('kebutuhans', function (Blueprint $table) {
                $table->dropConstrainedForeignId('disaster_event_id');
            });
        }

        if (Schema::hasTable('barang_keluars') && Schema::hasColumn('barang_keluars', 'disaster_event_id')) {
            Schema::table('barang_keluars', function (Blueprint $table) {
                $table->dropConstrainedForeignId('disaster_event_id');
            });
        }

        if (Schema::hasTable('barang_masuks') && Schema::hasColumn('barang_masuks', 'disaster_event_id')) {
            Schema::table('barang_masuks', function (Blueprint $table) {
                $table->dropConstrainedForeignId('disaster_event_id');
            });
        }

        if (Schema::hasTable('barangs') && Schema::hasColumn('barangs', 'disaster_event_id')) {
            Schema::table('barangs', function (Blueprint $table) {
                $table->dropConstrainedForeignId('disaster_event_id');
            });
        }
    }
};

