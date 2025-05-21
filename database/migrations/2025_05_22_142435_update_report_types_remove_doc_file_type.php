<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all report types with allowed_file_types
        $reportTypes = DB::table('report_types')
            ->whereNotNull('allowed_file_types')
            ->get();

        // Update each report type to remove 'doc' from allowed_file_types
        foreach ($reportTypes as $reportType) {
            $allowedTypes = json_decode($reportType->allowed_file_types, true);
            
            // Remove 'doc' from the allowed types if it exists
            if (is_array($allowedTypes) && in_array('doc', $allowedTypes)) {
                $allowedTypes = array_values(array_filter($allowedTypes, function($type) {
                    return $type !== 'doc';
                }));
                
                // Update the report type with the new allowed types
                DB::table('report_types')
                    ->where('id', $reportType->id)
                    ->update(['allowed_file_types' => json_encode($allowedTypes)]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be reversed as it would require knowing which report types
        // previously had 'doc' in their allowed_file_types
    }
};
