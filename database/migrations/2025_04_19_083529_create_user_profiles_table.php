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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
    
            // Basic Info
            $table->string('display_name')->nullable();
            $table->string('location')->nullable();
            $table->string('rally_fan_since')->nullable();
            $table->date('birthday')->nullable();
            $table->text('bio')->nullable();
    
            // Rally Interests
            $table->string('favorite_driver')->nullable();
            $table->string('favorite_car')->nullable();
            $table->string('favorite_event')->nullable();
            $table->string('favorite_game')->nullable();
            $table->text('car_setup_notes')->nullable();
    
            // Social Links
            $table->string('website')->nullable();
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();
            $table->string('twitter')->nullable();
            $table->string('twitch')->nullable();
    
            // Customization
            $table->string('profile_color')->nullable();
            $table->string('banner_image')->nullable();
            $table->enum('layout_style', ['compact', 'classic', 'photo-heavy'])->nullable();
    
            $table->timestamps();
        });
    }    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
