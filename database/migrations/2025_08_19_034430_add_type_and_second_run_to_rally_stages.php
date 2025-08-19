<?php

// database/migrations/2025_08_19_XXXXXX_add_type_and_second_run_to_rally_stages.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rally_stages', function (Blueprint $t) {
            $t->string('stage_type', 8)->default('SS'); // SS or SD
            $t->unsignedSmallInteger('second_ss_number')->nullable();
            $t->foreignId('second_rally_event_day_id')
              ->nullable()
              ->constrained('rally_event_days')
              ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rally_stages', function (Blueprint $t) {
            $t->dropColumn('stage_type');
            $t->dropColumn('second_ss_number');
            $t->dropConstrainedForeignId('second_rally_event_day_id');
        });
    }
};