<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('off_days', function (Blueprint $table) {
        $table->id(); // Fixed: Added the $
        $table->date('off_date'); 
        $table->time('start_time')->nullable(); 
        $table->time('end_time')->nullable();   
        $table->string('reason')->nullable();   
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('off_days');
    }
};
