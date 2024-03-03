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
        Schema::create('ratios', function (Blueprint $table) {
            $table->id();
            $table->string('series_id')->unique();
            $table->string('series');
            $table->string('staff_code');
            $table->string('leave_type')->nullable();
            $table->string('staff')->nullable();
            $table->string('entity');
            $table->string('shift_type');
            $table->string('division');
            $table->string('dept');
            $table->string('section');
            $table->float('working_days', 8, 2);
            $table->float('total_sl', 8, 2);
            $table->float('total_vl', 8, 2);
            $table->float('total_late', 8, 2);
            $table->float('total_early_exit', 8, 2);
            $table->float('total_lwop', 8, 2);
            $table->float('total_absent', 8, 2);
            $table->float('absent_ratio', 8, 2);
            $table->float('attendance_ratio', 8, 2);
            $table->float('sl_percentage', 8, 2);
            $table->float('vl_percentage', 8, 2);
            $table->float('late_percentage', 8, 2);
            $table->float('early_exit_percentage', 8, 2);
            $table->float('lwop_percentage', 8, 2);
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
        Schema::dropIfExists('ratios');
    }
};
