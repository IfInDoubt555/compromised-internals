<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('threads', function (Blueprint $table) {
            $table->enum('status', ['draft','scheduled','published'])->default('draft')->after('slug');
            $table->timestampTz('scheduled_for')->nullable()->after('status');
            $table->timestampTz('published_at')->nullable()->after('scheduled_for')->index();
        });
    }

    public function down(): void {
        Schema::table('threads', function (Blueprint $table) {
            $table->dropColumn(['status','scheduled_for','published_at']);
        });
    }
};