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
        Schema::table('complaints', function (Blueprint $table) {
            $table->string('status')->default('in_progress')->after('description'); // Status: 'in_progress', 'resolved'
            $table->text('admin_response')->nullable()->after('status'); // Admin's reply
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn(['status', 'admin_response']);
        });
    }
};
