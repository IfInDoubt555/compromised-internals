<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('affiliate_clicks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('brand', 40)->nullable()->index();
            $table->string('subid', 120)->nullable()->index();
            $table->text('url');                 // full destination URL
            $table->string('host')->index();     // parsed host part
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('ip', 45)->nullable();
            $table->text('ua')->nullable();
            $table->text('referer')->nullable();
            $table->timestamps();

            $table->index(['brand', 'created_at']);
            $table->index(['host', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_clicks');
    }
};