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
        // First, save the current cluster_id values
        $barangayClusterRelationships = DB::table('users')
            ->where('user_type', 'barangay')
            ->whereNotNull('cluster_id')
            ->select('id', 'cluster_id')
            ->get()
            ->keyBy('id')
            ->map(function ($item) {
                return $item->cluster_id;
            })
            ->toArray();

        // Drop the foreign key constraint
        Schema::table('users', function (Blueprint $table) {
            // Try to drop the foreign key constraint
            try {
                $table->dropForeign(['cluster_id']);
            } catch (\Exception $e) {
                // If it fails, the constraint might not exist or have a different name
                // Let's try with the naming convention Laravel uses
                try {
                    $table->dropForeign('users_cluster_id_foreign');
                } catch (\Exception $e) {
                    // If both fail, we'll continue anyway
                }
            }
        });

        // Drop the column and recreate it with the correct foreign key
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('cluster_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('cluster_id')->nullable();
            $table->foreign('cluster_id')
                ->references('id')
                ->on('clusters')
                ->onDelete('set null');
        });

        // Restore the cluster_id values
        foreach ($barangayClusterRelationships as $userId => $clusterId) {
            DB::table('users')
                ->where('id', $userId)
                ->update(['cluster_id' => $clusterId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a complex migration to reverse
        // It's recommended to have a backup before running this migration
    }
};
