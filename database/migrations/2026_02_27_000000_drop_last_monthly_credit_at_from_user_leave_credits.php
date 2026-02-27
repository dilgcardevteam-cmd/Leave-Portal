<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_leave_credits', function (Blueprint $table) {
            if (Schema::hasColumn('user_leave_credits', 'last_monthly_credit_at')) {
                $table->dropColumn('last_monthly_credit_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_leave_credits', function (Blueprint $table) {
            $table->dateTime('last_monthly_credit_at')->nullable()->after('credits_total');
        });
    }
};
