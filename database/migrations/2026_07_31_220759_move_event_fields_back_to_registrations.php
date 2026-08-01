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
     * The client wants these on the participant form itself (matching the
     * legacy form exactly), not only on the camp — reverses the earlier
     * "set once per camp" consolidation for these 4 fields specifically.
     */
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('campus')->nullable()->after('organisation');
            $table->string('adresse_campus')->nullable()->after('campus');
            $table->boolean('camp_de_jour')->nullable()->after('adresse_campus');
            $table->string('type_camp')->nullable()->after('camp_de_jour');
        });

        Schema::table('camps', function (Blueprint $table) {
            $table->dropColumn(['type_camp', 'campus', 'adresse_campus', 'camp_de_jour']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('camps', function (Blueprint $table) {
            $table->string('type_camp')->nullable();
            $table->string('campus')->nullable();
            $table->string('adresse_campus')->nullable();
            $table->boolean('camp_de_jour')->nullable();
        });

        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['campus', 'adresse_campus', 'camp_de_jour', 'type_camp']);
        });
    }
};
