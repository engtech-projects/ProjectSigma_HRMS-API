<?php

use App\Enums\RequestStatuses;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('request_13th_months', function (Blueprint $table) {
            $table->id();
            $table->date("date_requested");
            $table->date("date_from");
            $table->date("date_to");
            $table->json("employees");
            $table->integer("days_advance")->default(0);
            $table->nullableMorphs("charging");
            $table->json("metadata")->nullable();
            $table->json("approvals")->nullable();
            $table->enum("request_status", RequestStatuses::toArray())->default("Pending");
            $table->unsignedBigInteger("created_by");
            $table->timestamps();
            $table->softDeletes();
            $table->index(['request_status']);
            $table->index(['created_by']);
            $table->index(['date_requested']);
            $table->index(['date_from', 'date_to']);
            $table->foreign("created_by")->references("id")->on("users");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_13th_months');
    }
};
