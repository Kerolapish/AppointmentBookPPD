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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to the user account

            // The specific fields you asked for:
            $table->string('name');      // Name of the person visiting
            $table->string('ips');       // Institusi Pendidikan Swasta (Type form)
            $table->string('purpose');   // Purpose of visit
            $table->string('location');  // Location

            // Date and Time
            $table->date('date');        // Saved separately as requested
            $table->time('time');        // Saved separately as requested

            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
