<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // If it already exists (prod), skip creating it again
        if (Schema::hasTable('tag_thread')) {
            return;
        }

        Schema::create('tag_thread', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->foreignId('thread_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['tag_id', 'thread_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tag_thread');
    }
};