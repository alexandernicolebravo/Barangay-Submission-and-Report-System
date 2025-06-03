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
            $table->string('file_name')->nullable()->after('title');
            $table->bigInteger('file_size')->nullable()->after('file_path');
            $table->string('file_type')->nullable()->after('file_size');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null')->after('file_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issuances', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by']);
            $table->dropColumn(['file_name', 'file_size', 'file_type', 'uploaded_by']);
        });
    }
};
