<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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

            // Replace 'doc' with 'docx' if it exists
            if (in_array('doc', $allowedTypes)) {
                $allowedTypes = array_values(array_filter($allowedTypes, function($type) {
                    return $type !== 'doc';
                }));
                
                // Add 'docx' if it doesn't already exist
                if (!in_array('docx', $allowedTypes)) {
                    $allowedTypes[] = 'docx';
                }
                
                // Update the report type with the new allowed types
                DB::table('report_types')
                    ->where('id', $reportType->id)
                    ->update(['allowed_file_types' => json_encode($allowedTypes)]);
                
                Log::info('Updated report type ' . $reportType->id . ' to use docx instead of doc');
            }
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
