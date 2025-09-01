<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_profiles')) {
            return;
        }

        // 1) Add new boolean columns only if they don't exist yet
        Schema::table('user_profiles', function (Blueprint $t) {
            if (!Schema::hasColumn('user_profiles', 'show_location')) {
                $t->boolean('show_location')->default(true);
            }
            if (!Schema::hasColumn('user_profiles', 'show_birthday')) {
                $t->boolean('show_birthday')->default(false);
            }
            if (!Schema::hasColumn('user_profiles', 'show_socials')) {
                $t->boolean('show_socials')->default(true);
            }
            if (!Schema::hasColumn('user_profiles', 'show_favorites')) {
                $t->boolean('show_favorites')->default(true);
            }
            if (!Schema::hasColumn('user_profiles', 'show_car_setup_notes')) {
                $t->boolean('show_car_setup_notes')->default(false);
            }
        });

        // 2) Column tweaks WITHOUT doctrine/dbal (MySQL syntax). Guard by column existence.
        // Make profile_color VARCHAR(20) NULL
        if (Schema::hasColumn('user_profiles', 'profile_color')) {
            DB::statement("ALTER TABLE `user_profiles` MODIFY `profile_color` VARCHAR(20) NULL");
        }

        // Make banner_image VARCHAR(255) NULL
        if (Schema::hasColumn('user_profiles', 'banner_image')) {
            DB::statement("ALTER TABLE `user_profiles` MODIFY `banner_image` VARCHAR(255) NULL");
        }

        // Ensure layout_style exists and is VARCHAR(20) NULL DEFAULT 'card'
        if (Schema::hasColumn('user_profiles', 'layout_style')) {
            DB::statement("ALTER TABLE `user_profiles` MODIFY `layout_style` VARCHAR(20) NULL DEFAULT 'card'");
        } else {
            Schema::table('user_profiles', function (Blueprint $t) {
                $t->string('layout_style', 20)->nullable()->default('card');
            });
        }

        // 3) Add index on user_id if missing
        if (
            Schema::hasColumn('user_profiles', 'user_id') &&
            !$this->indexExists('user_profiles', 'user_profiles_user_id_index')
        ) {
            Schema::table('user_profiles', function (Blueprint $t) {
                $t->index('user_id'); // default name: user_profiles_user_id_index
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('user_profiles')) {
            return;
        }

        Schema::table('user_profiles', function (Blueprint $t) {
            if (Schema::hasColumn('user_profiles', 'show_location')) {
                $t->dropColumn('show_location');
            }
            if (Schema::hasColumn('user_profiles', 'show_birthday')) {
                $t->dropColumn('show_birthday');
            }
            if (Schema::hasColumn('user_profiles', 'show_socials')) {
                $t->dropColumn('show_socials');
            }
            if (Schema::hasColumn('user_profiles', 'show_favorites')) {
                $t->dropColumn('show_favorites');
            }
            if (Schema::hasColumn('user_profiles', 'show_car_setup_notes')) {
                $t->dropColumn('show_car_setup_notes');
            }

            // Drop the index if it exists
            if ($this->indexExists('user_profiles', 'user_profiles_user_id_index')) {
                $t->dropIndex('user_profiles_user_id_index');
            }
        });

        // (Intentionally not reverting the column type/nullable changes;
        //  if you need to, add matching ALTER TABLE statements here.)
    }

    /**
     * Check if an index exists on a table (MySQL).
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $db = DB::getDatabaseName();

        // information_schema.STATISTICS lists existing indexes
        return DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $db)
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $indexName)
            ->exists();
    }
};