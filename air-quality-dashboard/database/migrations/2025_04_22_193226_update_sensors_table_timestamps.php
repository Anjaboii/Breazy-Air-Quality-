public function up()
{
    Schema::table('sensors', function (Blueprint $table) {
        // If timestamps exist in wrong position, first remove them
        $table->dropTimestamps();
        
        // Add them back after is_active
        $table->timestamp('created_at')->nullable()->after('is_active');
        $table->timestamp('updated_at')->nullable()->after('created_at');
    });
}

public function down()
{
    Schema::table('sensors', function (Blueprint $table) {
        $table->dropTimestamps();
    });
}