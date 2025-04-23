<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAqiLocationsTable extends Migration
{
    public function up()
    {
        Schema::create('aqi_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('aqi', 5, 2)->nullable(); // AQI value fetched from the API
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('aqi_locations');
    }
}

