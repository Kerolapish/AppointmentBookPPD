<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            // User info (Auto-filled)
            $table->string('name');
            $table->string('email');

            // Complaint details
            $table->string('ips')->nullable();      // Institute name
            $table->string('location');             // Where it happened
            $table->date('incident_date');          // NEW: Date picker
            $table->string('category');             // NEW: Dropdown (replacing generic purpose)
            $table->text('description');            // NEW: Large text area
            $table->string('attachment')->nullable(); // NEW: File path for uploads

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
