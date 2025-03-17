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

        DB::table('job_applicants')->where('status', JobApplicationStatusEnums::CONTACT_EXTENDED->value)->update(['status' => JobApplicationStatusEnums::PROCESSING->value]);
        DB::table('job_applicants')->where('status', JobApplicationStatusEnums::INTERVIEWED->value)->update(['status' => JobApplicationStatusEnums::PROCESSING->value]);
        DB::table('job_applicants')->where('status', JobApplicationStatusEnums::FOR_HIRING->value)->update(['status' => JobApplicationStatusEnums::PROCESSING->value]);
        DB::table('job_applicants')->where('status', JobApplicationStatusEnums::INTERVIEWED->value)->update(['status' => JobApplicationStatusEnums::PROCESSING->value]);
        DB::table('job_applicants')->where('status', JobApplicationStatusEnums::REFERENCE_CHECK->value)->update(['status' => JobApplicationStatusEnums::PROCESSING->value]);
        DB::table('job_applicants')->where('status', JobApplicationStatusEnums::MEDICAL_EXAM->value)->update(['status' => JobApplicationStatusEnums::PROCESSING->value]);
        DB::table('job_applicants')->where('status', JobApplicationStatusEnums::CONTRACT_SIGNED->value)->update(['status' => JobApplicationStatusEnums::PROCESSING->value]);
        DB::table('job_applicants')->where('status', JobApplicationStatusEnums::REJECTED->value)->update(['status' => JobApplicationStatusEnums::AVAILABLE->value]);
        DB::table('job_applicants')->where('status', JobApplicationStatusEnums::PENDING->value)->update(['status' => JobApplicationStatusEnums::AVAILABLE->value]);
        DB::table('job_applicants')->where('status', JobApplicationStatusEnums::HIRED->value)->update(['status' => JobApplicationStatusEnums::HIRED->value]);

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
