<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scan_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->nullable()->constrained('invitations')->onDelete('cascade');
            $table->string('scanned_code');
            $table->foreignId('scanned_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['success', 'quota_full', 'invalid_code']);
            $table->string('message');
            $table->timestamp('scanned_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_logs');
    }
};