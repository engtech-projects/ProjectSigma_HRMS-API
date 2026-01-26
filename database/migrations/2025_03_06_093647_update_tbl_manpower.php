<?php

use App\Enums\RequestStatuses;
use App\Enums\FillStatuses;
use App\Enums\HiringStatuses;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::useNativeSchemaOperationsIfPossible();

        Schema::create('manpower_request_job_applicants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_applicants_id');
            $table->foreign('job_applicants_id')->references('id')->on('job_applicants');
            $table->unsignedBigInteger('manpowerrequests_id');
            $table->foreign('manpowerrequests_id')->references('id')->on('manpower_requests');
            $table->string("remarks")->nullable();
            $table->json('processing_checklist')->nullable();
            $table->enum('hiring_status', HiringStatuses::toArray());
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('job_applicants')->orderBy('id')->chunk(100, function ($applicants) {
            foreach ($applicants as $applicant) {
                if (!is_null($applicant->manpowerrequests_id)) {
                    $status = HiringStatuses::REJECTED->value;
                    if (in_array($applicant->status, HiringStatuses::toArray())) {
                        $status = $applicant->status;
                    }
                    $defaultProcessingChecklist = [
                        "Interviewed" => false,
                        "Tested" => false,
                        "Reference_Checked" => false,
                        "Medical_Examination" => false,
                        "Contact_Extended" => false,
                        "Contract_Signed" => false,
                    ];

                    if ($status === HiringStatuses::HIRED->value || $status === HiringStatuses::FOR_HIRING->value) {
                        $defaultProcessingChecklist = [
                            "Interviewed" => true,
                            "Tested" => true,
                            "Reference_Checked" => true,
                            "Medical_Examination" => true,
                            "Contact_Extended" => true,
                            "Contract_Signed" => true,
                        ];
                    }

                    DB::table('manpower_request_job_applicants')->insert([
                        'job_applicants_id' => $applicant->id,
                        'manpowerrequests_id' => $applicant->manpowerrequests_id,
                        'hiring_status' => $status,
                        'processing_checklist' => json_encode($defaultProcessingChecklist),
                        'created_at' => $applicant->created_at,
                        'updated_at' => $applicant->updated_at,
                    ]);
                }
            }
        });

        Schema::table('manpower_requests', function (Blueprint $table) {
            $table->enum('request_status', ['Approved', 'Disapproved', 'Pending', 'Cancelled', 'Filled', 'Hold', 'Voided', 'Denied'])->after('breakdown_details')->change();
            $table->enum('fill_status', FillStatuses::toArray())->after('breakdown_details')->default(FillStatuses::PENDING->value);
        });

        DB::table('manpower_requests')->where('request_status', FillStatuses::CANCELLED->value)->update(['fill_status' => FillStatuses::CANCELLED->value]);
        DB::table('manpower_requests')->where('request_status', FillStatuses::FILLED->value)->update(['fill_status' => FillStatuses::FILLED->value]);
        DB::table('manpower_requests')->where('request_status', FillStatuses::HOLD->value)->update(['fill_status' => FillStatuses::HOLD->value]);
        DB::table('manpower_requests')->where('request_status', RequestStatuses::APPROVED->value)->update(['fill_status' => FillStatuses::OPEN->value]);

        DB::table('manpower_requests')->where('request_status', FillStatuses::PENDING->value)->update(['request_status' => RequestStatuses::PENDING->value]);
        DB::table('manpower_requests')->where('request_status', RequestStatuses::APPROVED->value)->update(['request_status' => RequestStatuses::APPROVED->value]);
        DB::table('manpower_requests')->where('request_status', FillStatuses::FILLED->value)->update(['request_status' => RequestStatuses::APPROVED->value]);
        DB::table('manpower_requests')->where('request_status', FillStatuses::CANCELLED->value)->update(['request_status' => RequestStatuses::APPROVED->value]);
        DB::table('manpower_requests')->where('request_status', FillStatuses::HOLD->value)->update(['request_status' => RequestStatuses::APPROVED->value]);
        DB::table('manpower_requests')->where('request_status', 'Disapproved')->update(['request_status' => RequestStatuses::DENIED->value]);

        Schema::table('manpower_requests', function (Blueprint $table) {
            $table->enum('request_status', RequestStatuses::toArray())->after('breakdown_details')->change();
        });

        Schema::useNativeSchemaOperationsIfPossible(false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::useNativeSchemaOperationsIfPossible();

        DB::transaction(function () {
            Schema::table('manpower_requests', function (Blueprint $table) {
                $table->enum('request_status', ['Approved', 'Disapproved', 'Pending', 'Cancelled', 'Filled', 'Hold', 'Voided', 'Denied'])->change();
            });

            DB::table('manpower_requests')->where('fill_status', FillStatuses::PENDING->value)->update(['request_status' => FillStatuses::PENDING->value]);
            DB::table('manpower_requests')->where('fill_status', FillStatuses::FILLED->value)->update(['request_status' => FillStatuses::FILLED->value]);
            DB::table('manpower_requests')->where('fill_status', FillStatuses::CANCELLED->value)->update(['request_status' => FillStatuses::CANCELLED->value]);
            DB::table('manpower_requests')->where('fill_status', FillStatuses::HOLD->value)->update(['request_status' => FillStatuses::HOLD->value]);
            DB::table('manpower_requests')->where('request_status', RequestStatuses::DENIED->value)->update(['request_status' => 'Disapproved']);
            DB::table('manpower_requests')->where('fill_status', RequestStatuses::APPROVED->value)->update(['request_status' => RequestStatuses::APPROVED->value]);
            DB::table('manpower_requests')->where('fill_status', FillStatuses::OPEN->value)->update(['fill_status' => RequestStatuses::APPROVED->value]);

            Schema::table('manpower_requests', function (Blueprint $table) {
                $table->dropColumn('fill_status');
            });

            Schema::table('manpower_requests', function (Blueprint $table) {
                $table->enum('request_status', ['Approved', 'Disapproved', 'Pending', 'Cancelled', 'Filled', 'Hold'])->change();
            });

            Schema::dropIfExists('manpower_request_job_applicants');
        });

        Schema::useNativeSchemaOperationsIfPossible(false);
    }
};
