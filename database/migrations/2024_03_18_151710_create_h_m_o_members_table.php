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
        Schema::create('hmo_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hmo_id');
            $table->foreign('hmo_id')->references('id')->on('hmo');
            $table->enum("member_type", ["employee", "external(addon)"]);
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->string('member_name');
            $table->unsignedBigInteger('member_belongs_to')->nullable();
            $table->foreign('member_belongs_to')->references('id')->on('employees');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hmo_members');
    }
};
