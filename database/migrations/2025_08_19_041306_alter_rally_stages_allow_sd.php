<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // If you already added stage_type/second_* columns, leave them.
        // Key change: ss_number -> nullable
        // Use raw SQL to avoid requiring doctrine/dbal on prod.
        DB::statement('ALTER TABLE rally_stages MODIFY ss_number INT UNSIGNED NULL');

        // If you *only* had a unique index on ss_number, replace with composite:
        // (Safe to run; wrap in try to avoid errors if already set)
        try { DB::statement('ALTER TABLE rally_stages DROP INDEX rally_stages_ss_number_unique'); } catch (\Throwable $e) {}
        try { DB::statement('CREATE UNIQUE INDEX rally_stages_event_ss_unique ON rally_stages (rally_event_id, ss_number)'); } catch (\Throwable $e) {}

        // If stage_type didn’t exist or could be NULL in old rows, default to 'SS'
        try { DB::statement("UPDATE rally_stages SET stage_type = 'SS' WHERE stage_type IS NULL OR stage_type = ''"); } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        // Revert ss_number back to NOT NULL (pick a default as needed)
        DB::statement('ALTER TABLE rally_stages MODIFY ss_number INT UNSIGNED NOT NULL');
        try { DB::statement('DROP INDEX rally_stages_event_ss_unique ON rally_stages'); } catch (\Throwable $e) {}
        // If you had an old single-column unique, you could recreate it here.
    }
};