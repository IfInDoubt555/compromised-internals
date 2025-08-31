<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $t) {
            // Privacy toggles
            $t->boolean('show_location')->default(true);
            $t->boolean('show_birthday')->default(false);
            $t->boolean('show_socials')->default(true);
            $t->boolean('show_favorites')->default(true);
            $t->boolean('show_car_setup_notes')->default(false);

            // Theming (already have profile_color/banner_image/layout_style; keep nullable)
            $t->string('profile_color', 20)->nullable()->change();
            $t->string('banner_image')->nullable()->change();
            $t->string('layout_style', 20)->nullable()->default('card');

            // Index for lookups if you later add slugs
            $t->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $t) {
            $t->dropColumn([
                'show_location','show_birthday','show_socials','show_favorites','show_car_setup_notes',
            ]);
            $t->dropIndex(['user_id']);
        });
    }
};