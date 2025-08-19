<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rally_events', function (Blueprint $table) {
            $table->string('map_embed_url', 1000)->nullable()->after('description');
        });

        // Optional backfill: copy first stage embed into its event
        DB::table('rally_events')->orderBy('id')->chunkById(100, function ($events) {
            foreach ($events as $ev) {
                $url = DB::table('rally_stages')
                    ->where('rally_event_id', $ev->id)
                    ->whereNotNull('map_embed_url')
                    ->value('map_embed_url');
                if ($url) {
                    DB::table('rally_events')->where('id', $ev->id)->update(['map_embed_url' => $url]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('rally_events', function (Blueprint $table) {
            $table->dropColumn('map_embed_url');
        });
    }
};