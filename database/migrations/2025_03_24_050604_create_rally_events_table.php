<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rally_events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('slug')->unique();
            //$table->foreignId('user_id')->constrained()->onDelete('cascade'); // Event creator
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rally_events');
    }
};
