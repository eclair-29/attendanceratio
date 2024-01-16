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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('staff_code');
            $table->foreign('staff_code')->references('staff_code')->on('staff_base_details')->onDelete('cascade');
            $table->string('date');
            $table->string('entity');
            $table->string('shift')->nullable();
            $table->string('shift_st')->nullable();
            $table->string('att_st')->nullable();
            $table->string('shift_end')->nullable();
            $table->string('att_end')->nullable();
            $table->string('late');
            $table->string('early_exit');
            $table->string('holiday')->nullable();
            $table->string('leave_type')->nullable();
            $table->string('np')->nullable();
            $table->string('other_leaves')->nullable();
            $table->string('tardy')->nullable();
            $table->string('ut')->nullable();
            $table->string('lwop')->nullable();
            $table->string('adjust');
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
        Schema::dropIfExists('attendances');
    }
};
