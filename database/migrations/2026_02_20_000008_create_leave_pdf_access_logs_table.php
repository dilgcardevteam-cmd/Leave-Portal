<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leave_pdf_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_id')->constrained('leaves')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('role')->nullable();
            $table->string('ip')->nullable();
            $table->timestamp('accessed_at')->nullable();
            $table->timestamps();
            $table->index(['leave_id','user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_pdf_access_logs');
    }
};

