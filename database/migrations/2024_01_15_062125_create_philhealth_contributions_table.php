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
        Schema::create('philhealth_contributions', function (Blueprint $table) {
            $table->id();
            $table->float("range_from");
            $table->float("range_to");
            $table->float("share");
            $table->enum('share_type', ['Amount', 'Percentage']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('philhealth_contributions');
    }
};
