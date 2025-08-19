<?php

// database/migrations/2025_08_19_000001_backfill_event_map_embed.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        DB::transaction(function () {
            $rows = DB::table('rally_stages')
                ->whereNotNull('map_embed_url')
                ->select('rally_event_id', DB::raw('MIN(id) as stage_id'))
                ->groupBy('rally_event_id')->get();

            foreach ($rows as $row) {
                $url = DB::table('rally_stages')->where('id', $row->stage_id)->value('map_embed_url');
                if ($url) {
                    DB::table('rally_events')->where('id', $row->rally_event_id)
                        ->whereNull('map_embed_url')->update(['map_embed_url' => $url]);
                }
            }

            DB::table('rally_stages')->update(['map_embed_url' => null]); // optional cleanup
        });
    }
    public function down(): void {
        // no-op (can’t reliably restore per-stage values)
    }
};