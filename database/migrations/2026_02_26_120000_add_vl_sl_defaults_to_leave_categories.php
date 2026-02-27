<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leave_categories', function (Blueprint $table) {
            $table->decimal('vl_default_credits', 8, 3)->default(0)->after('default_credits');
            $table->decimal('sl_default_credits', 8, 3)->default(0)->after('vl_default_credits');
        });
    }

    public function down(): void
    {
        Schema::table('leave_categories', function (Blueprint $table) {
            $table->dropColumn(['vl_default_credits', 'sl_default_credits']);
        });
    }
};
