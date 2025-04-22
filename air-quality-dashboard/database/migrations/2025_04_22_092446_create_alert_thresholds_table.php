<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertThresholdsTable extends Migration
{
    public function up()
    {
        Schema::create('alert_thresholds', function (Blueprint $table) {
            $table->id();
            $table->string('level_name');
            $table->float('min_value');
            $table->float('max_value');
            $table->string('color_code');
            $table->text('health_implications')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('alert_thresholds');
    }
}