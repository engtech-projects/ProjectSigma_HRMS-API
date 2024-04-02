<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_relatedpeople', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->string("relationship");
            $table->enum('type', ['contact person','dependent/children','father','mother','spouse','reference']);
            $table->string("name");
            $table->date("date_of_birth");
            $table->string("street");
            $table->string("brgy");
            $table->string("city");
            $table->string("zip");
            $table->string("province");
            $table->string("occupation");
            $table->string("contact_no");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_relatedpeople');
    }
};
