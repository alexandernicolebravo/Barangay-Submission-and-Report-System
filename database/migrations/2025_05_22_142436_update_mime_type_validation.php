<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the validation rules in the database to use the correct MIME types
        // This is necessary because Laravel's validation rules for MIME types are different
        // from the actual MIME types returned by the browser

        // Get all report types
        $reportTypes = DB::table('report_types')->get();

        foreach ($reportTypes as $reportType) {
            // Skip if no allowed file types are defined
            if (empty($reportType->allowed_file_types)) {
                continue;
            }

            // Decode the allowed file types
            $allowedTypes = json_decode($reportType->allowed_file_types, true);

            // Skip if the allowed file types are not an array
            if (!is_array($allowedTypes)) {
                continue;
            }

            // Update the report type with the new allowed file types
            DB::table('report_types')
                ->where('id', $reportType->id)
                ->update(['allowed_file_types' => json_encode($allowedTypes)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be reversed
    }
};
