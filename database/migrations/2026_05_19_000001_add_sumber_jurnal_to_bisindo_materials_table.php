<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bisindo_materials') && !Schema::hasColumn('bisindo_materials', 'sumber_jurnal')) {
            Schema::table('bisindo_materials', function (Blueprint $table) {
                $table->text('sumber_jurnal')->nullable()->after('deskripsi');
            });
        }

        if (Schema::hasTable('bisindo_materials') && Schema::hasColumn('bisindo_materials', 'tingkat')) {
            DB::table('bisindo_materials')->where('tingkat', 'Dasar')->update(['tingkat' => 'Pemula']);
            DB::table('bisindo_materials')->where('tingkat', 'Lanjut')->update(['tingkat' => 'Lanjutan']);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bisindo_materials') && Schema::hasColumn('bisindo_materials', 'sumber_jurnal')) {
            Schema::table('bisindo_materials', function (Blueprint $table) {
                $table->dropColumn('sumber_jurnal');
            });
        }
    }
};

