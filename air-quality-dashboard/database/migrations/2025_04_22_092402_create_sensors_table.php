<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSensorsTable extends Migration
{
    public function up()
    {
        Schema::create('sensors', function (Blueprint $table) {
            $table->id();
            $table->string('sensor_id')->unique();
            $table->string('name');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('location_description');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sensors');
    }
}