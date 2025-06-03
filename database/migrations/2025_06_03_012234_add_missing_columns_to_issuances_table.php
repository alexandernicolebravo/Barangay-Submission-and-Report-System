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
        Schema::table('issuances', function (Blueprint $table) {
            // Check if columns exist before adding them
            if (!Schema::hasColumn('issuances', 'file_name')) {
                $table->string('file_name')->nullable()->after('title');
            }
            if (!Schema::hasColumn('issuances', 'file_size')) {
                $table->bigInteger('file_size')->nullable()->after('file_path');
            }
            if (!Schema::hasColumn('issuances', 'file_type')) {
                $table->string('file_type')->nullable()->after('file_size');
            }
            if (!Schema::hasColumn('issuances', 'uploaded_by')) {
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null')->after('file_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issuances', function (Blueprint $table) {
            // Only drop columns that were added by this migration
            if (Schema::hasColumn('issuances', 'uploaded_by')) {
                $table->dropForeign(['uploaded_by']);
                $table->dropColumn('uploaded_by');
            }
            if (Schema::hasColumn('issuances', 'file_type')) {
                $table->dropColumn('file_type');
            }
            if (Schema::hasColumn('issuances', 'file_size')) {
                $table->dropColumn('file_size');
            }
            // Note: We don't drop file_name as it was created by the original table creation migration
        });
    }
};
