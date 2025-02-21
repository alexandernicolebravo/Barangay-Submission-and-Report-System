<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('barangay_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barangay_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->enum('status', ['Pending', 'Completed', 'Rejected'])->default('Pending');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('barangay_files');
    }
};
