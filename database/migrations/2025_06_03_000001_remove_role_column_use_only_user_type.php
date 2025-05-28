<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the role column since we only want to use user_type
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        // Ensure all users have a user_type value
        DB::table('users')
            ->whereNull('user_type')
            ->update(['user_type' => 'barangay']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add the role column back
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'facilitator', 'barangay'])->nullable()->after('password');
        });

        // Sync role column with user_type column
        DB::table('users')
            ->whereNotNull('user_type')
            ->update([
                'role' => DB::raw('user_type')
            ]);
    }
};
