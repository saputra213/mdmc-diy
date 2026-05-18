<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('disaster_event_photos')) {
            return;
        }

        Schema::create('disaster_event_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disaster_event_id')->constrained('disaster_events')->cascadeOnDelete();
            $table->string('image_path', 2048);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['disaster_event_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disaster_event_photos');
    }
};

