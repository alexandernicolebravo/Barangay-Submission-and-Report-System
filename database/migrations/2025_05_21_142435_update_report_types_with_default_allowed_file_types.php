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
        // Set default allowed file types for all report types (without dots)
        $defaultFileTypes = [
            'pdf', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'zip', 'rar'
        ];

        // Update all report types to have the default allowed file types
        DB::table('report_types')
            ->whereNull('allowed_file_types')
            ->update(['allowed_file_types' => json_encode($defaultFileTypes)]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
