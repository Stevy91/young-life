<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Introduces the missing hierarchy level discovered from the legacy
     * dashboard: what were seeded as flat "zones" (25 arrondissement names)
     * are actually one level too low. The real "zone" is the Métro region
     * (5 of them, gates sidebar/user access); each zone has several
     * arrondissements; clubs and registrations belong to an arrondissement,
     * not directly to a zone. Camps stay directly on zone (every camp
     * belongs to exactly one Métro region — there is no "national" camp).
     */
    public function up(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('zone_id');
            $table->foreignId('arrondissement_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        Schema::table('registrations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('zone_id');
            $table->foreignId('arrondissement_id')->nullable()->after('club_id')->constrained()->nullOnDelete();
            $table->dropColumn('role');
            $table->foreignId('camp_category_id')->nullable()->after('camp_id')->constrained()->nullOnDelete();
        });

        // The 25 rows here were arrondissement names mislabeled as zones;
        // replace them with the 5 real Métro zones before making camps.zone_id
        // required (safe: no camps exist yet at this point in the project).
        // A plain delete (not truncate) avoids MySQL's "cannot truncate a
        // table referenced by a foreign key" restriction from camps.zone_id.
        DB::table('zones')->delete();

        Schema::table('camps', function (Blueprint $table) {
            // The existing FK was created with ->nullOnDelete(), which is
            // incompatible with a NOT NULL column — drop and recreate it
            // with ->restrictOnDelete() instead (a zone with camps can't be
            // deleted outright, which is the right behavior anyway).
            $table->dropForeign(['zone_id']);
        });

        Schema::table('camps', function (Blueprint $table) {
            $table->unsignedBigInteger('zone_id')->nullable(false)->change();
            $table->foreign('zone_id')->references('id')->on('zones')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('camps', function (Blueprint $table) {
            $table->dropForeign(['zone_id']);
        });

        Schema::table('camps', function (Blueprint $table) {
            $table->unsignedBigInteger('zone_id')->nullable()->change();
            $table->foreign('zone_id')->references('id')->on('zones')->nullOnDelete();
        });

        Schema::table('registrations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('arrondissement_id');
            $table->dropConstrainedForeignId('camp_category_id');
            $table->string('role')->nullable();
            $table->foreignId('zone_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('clubs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('arrondissement_id');
            $table->foreignId('zone_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};
