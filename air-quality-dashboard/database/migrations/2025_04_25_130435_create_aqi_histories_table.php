<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('aqi_histories', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('location_id'); // Unsigned foreign key
        $table->foreign('location_id')->references('id')->on('aqi_locations')->onDelete('cascade'); // Foreign key constraint
        $table->integer('aqi');
        $table->timestamp('date')->useCurrent();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aqi_histories');
    }
};
