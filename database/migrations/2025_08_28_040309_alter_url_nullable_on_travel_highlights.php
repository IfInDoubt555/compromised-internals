<?php

// database/migrations/XXXX_XX_XX_XXXXXX_alter_url_nullable_on_travel_highlights.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('travel_highlights', function (Blueprint $table) {
            $table->string('url', 2048)->nullable()->change();
        });
    }
    public function down(): void {
        Schema::table('travel_highlights', function (Blueprint $table) {
            $table->string('url', 2048)->nullable(false)->change();
        });
    }
};