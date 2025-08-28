<?php

// database/migrations/2025_08_27_000000_create_travel_highlights_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('travel_highlights', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->nullable(); // optional link to your events table
            $table->string('title');                             // e.g., "Rallye Monte-Carlo — Plan Trip"
            $table->string('url');                               // internal or external
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            // $table->foreign('event_id')->references('id')->on('events')->nullOnDelete(); // optional
        });
    }
    public function down(): void { Schema::dropIfExists('travel_highlights'); }
};