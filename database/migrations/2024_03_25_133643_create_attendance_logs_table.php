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
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('time');
            $table->enum('log_type', ["In", "Out"]);
            $table->enum('attendance_type', ['Manual', 'Fingerprint', 'Facial', 'QR Code', 'Password', 'Biometric_Machine_Face', 'Biometric_Machine_Finger']);
            $table->unsignedBigInteger("project_id");
            $table->unsignedBigInteger("department_id");
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
