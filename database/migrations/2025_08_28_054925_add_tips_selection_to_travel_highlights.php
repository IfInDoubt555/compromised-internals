<?php

// database/migrations/XXXX_XX_XX_XXXXXX_add_tips_selection_to_travel_highlights.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('travel_highlights', function (Blueprint $t) {
            $t->json('tips_selection')->nullable()->after('tips_md');
        });
    }
    public function down(): void {
        Schema::table('travel_highlights', function (Blueprint $t) {
            $t->dropColumn('tips_selection');
        });
    }
};