<?php

// database/migrations/2025_08_19_000001_create_event_days_and_stages.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rally_event_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rally_event_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('label')->nullable(); // e.g. "Thursday 28"
            $table->timestamps();
            $table->unique(['rally_event_id','date']);
        });

        Schema::create('rally_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rally_event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rally_event_day_id')->nullable()->constrained('rally_event_days')->nullOnDelete();
            $table->unsignedInteger('ss_number');               // 1..N
            $table->string('name');                              // “Cambyretá”
            $table->decimal('distance_km', 6, 2)->nullable();    // 0–999.99
            $table->boolean('is_super_special')->default(false);
            $table->dateTime('start_time_local')->nullable();    // event local tz
            $table->dateTime('second_pass_time_local')->nullable();
            $table->string('map_image_url')->nullable();         // small red-line image
            $table->string('map_embed_url')->nullable();         // Google My Maps / OSM iframe
            $table->string('gpx_path')->nullable();              // downloadable file path
            $table->json('spectator_zones')->nullable();         // [{name, lat, lng, note}]
            $table->timestamps();
            $table->unique(['rally_event_id','ss_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rally_stages');
        Schema::dropIfExists('rally_event_days');
    }
};