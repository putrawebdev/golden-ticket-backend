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
        Schema::table('invitations', function (Blueprint $table) {
            // Option: 'pending', 'attending', 'declined'
            $table->string('rsvp_status')->default('pending')->after('seat_area');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            // Option: 'pending', 'attending', 'declined'
            $table->string('rsvp_status')->default('pending')->after('seat_area');
        });
    }
};
