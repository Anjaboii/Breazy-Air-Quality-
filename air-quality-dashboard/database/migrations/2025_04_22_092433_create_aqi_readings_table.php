<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAqiReadingsTable extends Migration
{
    public function up()
    {
        Schema::create('aqi_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sensor_id')->constrained()->onDelete('cascade');
            $table->float('aqi_value');
            $table->float('pm25')->nullable();
            $table->float('pm10')->nullable();
            $table->float('o3')->nullable();
            $table->float('no2')->nullable();
            $table->float('so2')->nullable();
            $table->float('co')->nullable();
            $table->string('category');
            $table->timestamp('reading_timestamp');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('aqi_readings');
    }
}