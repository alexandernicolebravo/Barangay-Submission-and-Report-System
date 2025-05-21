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
        // Add updates_allowed column to weekly_reports table
        Schema::table('weekly_reports', function (Blueprint $table) {
            $table->boolean('updates_allowed')->default(false)->after('remarks');
        });

        // Add updates_allowed column to monthly_reports table
        Schema::table('monthly_reports', function (Blueprint $table) {
            $table->boolean('updates_allowed')->default(false)->after('remarks');
        });

        // Add updates_allowed column to quarterly_reports table
        Schema::table('quarterly_reports', function (Blueprint $table) {
            $table->boolean('updates_allowed')->default(false)->after('remarks');
        });

        // Add updates_allowed column to semestral_reports table
        Schema::table('semestral_reports', function (Blueprint $table) {
            $table->boolean('updates_allowed')->default(false)->after('remarks');
        });

        // Add updates_allowed column to annual_reports table
        Schema::table('annual_reports', function (Blueprint $table) {
            $table->boolean('updates_allowed')->default(false)->after('remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove updates_allowed column from weekly_reports table
        Schema::table('weekly_reports', function (Blueprint $table) {
            $table->dropColumn('updates_allowed');
        });

        // Remove updates_allowed column from monthly_reports table
        Schema::table('monthly_reports', function (Blueprint $table) {
            $table->dropColumn('updates_allowed');
        });

        // Remove updates_allowed column from quarterly_reports table
        Schema::table('quarterly_reports', function (Blueprint $table) {
            $table->dropColumn('updates_allowed');
        });

        // Remove updates_allowed column from semestral_reports table
        Schema::table('semestral_reports', function (Blueprint $table) {
            $table->dropColumn('updates_allowed');
        });

        // Remove updates_allowed column from annual_reports table
        Schema::table('annual_reports', function (Blueprint $table) {
            $table->dropColumn('updates_allowed');
        });
    }
};
