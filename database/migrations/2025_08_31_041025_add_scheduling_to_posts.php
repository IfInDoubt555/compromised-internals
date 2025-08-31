<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('posts', function (Blueprint $table) {
            // keep existing 'status' column (moderation). Add separate publish fields.
            if (!Schema::hasColumn('posts', 'publish_status')) {
                $table->enum('publish_status', ['draft','scheduled','published'])
                      ->default('draft')
                      ->after('slug');
            }
            if (!Schema::hasColumn('posts', 'scheduled_for')) {
                $table->timestampTz('scheduled_for')->nullable()->after('publish_status');
            }
            if (!Schema::hasColumn('posts', 'published_at')) {
                $table->timestampTz('published_at')->nullable()->after('scheduled_for')->index();
            }
        });
    }

    public function down(): void {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'published_at'))   $table->dropColumn('published_at');
            if (Schema::hasColumn('posts', 'scheduled_for'))  $table->dropColumn('scheduled_for');
            if (Schema::hasColumn('posts', 'publish_status')) $table->dropColumn('publish_status');
        });
    }
};