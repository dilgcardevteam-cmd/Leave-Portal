<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leave_approval_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_id')->constrained('leaves')->onDelete('cascade');
            $table->enum('step', ['hr','dc','rd','ard']);
            $table->enum('action', ['approved','rejected']);
            $table->text('comment')->nullable();
            $table->foreignId('acted_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('acted_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['leave_id','step']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_approval_logs');
    }
};

