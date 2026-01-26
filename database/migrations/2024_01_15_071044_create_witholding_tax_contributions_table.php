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
        Schema::create('witholding_tax_contributions', function (Blueprint $table) {
            $table->id();
            $table->float("range_from");
            $table->float("range_to");
            $table->enum('term', ['Daily','Weekly','Semi-Monthly','Monthly']);
            $table->float("tax_base");
            $table->float("tax_amount");
            $table->float("tax_percent_over_base");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('witholding_tax_contributions');
    }
};
