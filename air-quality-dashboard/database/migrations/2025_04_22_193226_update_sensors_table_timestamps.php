<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSensorsTableTimestamps extends Migration
{
    public function up()
    {
        Schema::table('sensors', function (Blueprint $table) {
            $table->dropTimestamps(); // Remove existing timestamps
            $table->timestamp('created_at')->nullable()->after('is_active');  // Add new timestamp field
            $table->timestamp('updated_at')->nullable()->after('created_at'); // Add new timestamp field
        });
    }

    public function down()
    {
        Schema::table('sensors', function (Blueprint $table) {
            $table->dropTimestamps(); // Rollback the timestamps
        });
    }
}
