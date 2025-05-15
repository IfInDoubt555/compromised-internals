<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rally_events', function (Blueprint $table) {
            $table->string('championship')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('rally_events', function (Blueprint $table) {
            $table->dropColumn('championship');
        });
    }
};
