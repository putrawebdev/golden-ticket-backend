<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code')->unique()->index();
            $table->enum('type', ['school', 'vip', 'parent']);
            
            // Kolom spesifik per kategori
            $table->string('school_name')->nullable();
            $table->json('teacher_names')->nullable();
            $table->string('guest_name')->nullable();
            $table->string('parent_1_name')->nullable();
            $table->string('parent_2_name')->nullable();
            
            // Lokasi & Kuota
            $table->string('seat_area');
            $table->unsignedInteger('scan_count')->default(0);
            $table->unsignedInteger('max_scans')->default(1);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};