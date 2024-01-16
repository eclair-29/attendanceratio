<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_base_details', function (Blueprint $table) {
            $table->id();
            $table->string('staff_code')->unique();
            $table->string('status');
            $table->string('shift_type');
            $table->string('dept');
            $table->string('section');
            $table->string('division');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_base_details');
    }
};
