<?php

// database/migrations/2025_08_28_000001_create_travel_settings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('travel_highlights', function (Blueprint $table) {
            $table->string('kind', 20)->default('highlight')->index(); // 'highlight' | 'tips'
            $table->text('tips_md')->nullable();                       // only used when kind='tips'
        });
    }

    public function down(): void {
        Schema::table('travel_highlights', function (Blueprint $table) {
            $table->dropColumn(['kind','tips_md']);
        });
    }
};