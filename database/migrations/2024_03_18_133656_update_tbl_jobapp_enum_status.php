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
        Schema::table('job_applicants', function (Blueprint $table) {
            $table->enum('status', ['Contract Extended','Pending','Interviewed','Rejected','Hired','For Hiring','Test,Interview','Reference Checking','Medical Examination','Contract Signed'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applicants', function (Blueprint $table) {
            $table->enum('status', ['Contract Extended','Pending','Interviewed','Rejected','Hired','For Hiring','Test,Interview','Reference Checking','Medical Examination','Contract Signed'])->change();
        });
    }
};
