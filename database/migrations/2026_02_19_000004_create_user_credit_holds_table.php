<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_credit_holds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('leave_id')->constrained()->onDelete('cascade');
            $table->foreignId('leave_category_id')->constrained('leave_categories')->onDelete('cascade');
            $table->decimal('amount', 12, 3)->default(0);
            $table->enum('status', ['held', 'released', 'applied'])->default('held');
            $table->timestamps();
            $table->unique(['leave_id']); // one hold per leave
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_credit_holds');
    }
};

