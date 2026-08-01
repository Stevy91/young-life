<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * These are all "about the event" fields from the legacy form
     * ("RENSEIGNEMENTS SUR L'EVENEMENT") — set once per camp rather than
     * repeated on every single registration, same reasoning already applied
     * to date_debut/date_fin/nb_nuits.
     */
    public function up(): void
    {
        Schema::table('camps', function (Blueprint $table) {
            $table->string('type_camp')->nullable()->after('statut');
            $table->string('campus')->nullable()->after('type_camp');
            $table->string('adresse_campus')->nullable()->after('campus');
            $table->boolean('camp_de_jour')->nullable()->after('adresse_campus');
        });

        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn('campus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('camps', function (Blueprint $table) {
            $table->dropColumn(['type_camp', 'campus', 'adresse_campus', 'camp_de_jour']);
        });

        Schema::table('registrations', function (Blueprint $table) {
            $table->string('campus')->nullable();
        });
    }
};
