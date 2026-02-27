<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_leave_credits', function (Blueprint $table) {
            $table->decimal('credits_total', 12, 3)->default(100)->after('sl_total');
        });
    }

    public function down(): void
    {
        Schema::table('user_leave_credits', function (Blueprint $table) {
            $table->dropColumn('credits_total');
        });
    }
};

