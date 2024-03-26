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
        Schema::create('travel_orders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId("requesting_office");
            $table->foreign('requesting_office')->references('id')->on('departments');
            $table->string('destination');
            $table->string('purpose_of_travel');
            $table->date('date_and_time_of_travel');
            $table->bigInteger('duration_of_travel');
            $table->string('means_of_transportation');
            $table->string('remarks');
            $table->foreignId("requested_by");
            $table->foreign('requested_by')->references('id')->on('users');
            $table->json("approvals");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_orders');
    }
};
