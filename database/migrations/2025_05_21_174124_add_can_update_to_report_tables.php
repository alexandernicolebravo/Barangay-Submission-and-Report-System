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
        // Add can_update column to weekly_reports table
        Schema::table('weekly_reports', function (Blueprint $table) {
            $table->boolean('can_update')->default(false)->after('remarks');
        });

        // Add can_update column to monthly_reports table
        Schema::table('monthly_reports', function (Blueprint $table) {
            $table->boolean('can_update')->default(false)->after('remarks');
        });

        // Add can_update column to quarterly_reports table
        Schema::table('quarterly_reports', function (Blueprint $table) {
            $table->boolean('can_update')->default(false)->after('remarks');
        });

        // Add can_update column to semestral_reports table
        Schema::table('semestral_reports', function (Blueprint $table) {
            $table->boolean('can_update')->default(false)->after('remarks');
        });

        // Add can_update column to annual_reports table
        Schema::table('annual_reports', function (Blueprint $table) {
            $table->boolean('can_update')->default(false)->after('remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove can_update column from weekly_reports table
        Schema::table('weekly_reports', function (Blueprint $table) {
            $table->dropColumn('can_update');
        });

        // Remove can_update column from monthly_reports table
        Schema::table('monthly_reports', function (Blueprint $table) {
            $table->dropColumn('can_update');
        });

        // Remove can_update column from quarterly_reports table
        Schema::table('quarterly_reports', function (Blueprint $table) {
            $table->dropColumn('can_update');
        });

        // Remove can_update column from semestral_reports table
        Schema::table('semestral_reports', function (Blueprint $table) {
            $table->dropColumn('can_update');
        });

        // Remove can_update column from annual_reports table
        Schema::table('annual_reports', function (Blueprint $table) {
            $table->dropColumn('can_update');
        });
    }
};
