<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\JobApplicationStatusEnums;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::useNativeSchemaOperationsIfPossible();

        Schema::table('job_applicants', function (Blueprint $table) {
            $table->enum('status', ['Available', 'Processing', 'Not Available', 'Contact Extended','Pending','Interviewed','Rejected','Hired','For Hiring','Test,Interview','Reference Checking','Medical Examination','Contract Signed'])->change();
        });

        DB::table('job_applicants')->where('status', "Contact Extended")->update(['status' => JobApplicationStatusEnums::PROCESSING->value]);
        DB::table('job_applicants')->where('status', "Interviewed")->update(['status' => JobApplicationStatusEnums::PROCESSING->value]);
        DB::table('job_applicants')->where('status', "For Hiring")->update(['status' => JobApplicationStatusEnums::PROCESSING->value]);
        DB::table('job_applicants')->where('status', "Interviewed")->update(['status' => JobApplicationStatusEnums::PROCESSING->value]);
        DB::table('job_applicants')->where('status', "Reference Checking")->update(['status' => JobApplicationStatusEnums::PROCESSING->value]);
        DB::table('job_applicants')->where('status', "Medical Examination")->update(['status' => JobApplicationStatusEnums::PROCESSING->value]);
        DB::table('job_applicants')->where('status', "Contract Signed")->update(['status' => JobApplicationStatusEnums::PROCESSING->value]);
        DB::table('job_applicants')->where('status', "Rejected")->update(['status' => JobApplicationStatusEnums::AVAILABLE->value]);
        DB::table('job_applicants')->where('status', "Pending")->update(['status' => JobApplicationStatusEnums::AVAILABLE->value]);
        DB::table('job_applicants')->where('status', "Hired")->update(['status' => JobApplicationStatusEnums::HIRED->value]);

        Schema::table('job_applicants', function (Blueprint $table) {
            $table->enum('status', ['Available', 'Processing', 'Not Available', 'Hired'])->change();
        });

        Schema::useNativeSchemaOperationsIfPossible(false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applicants', function (Blueprint $table) {
        });
    }
};
