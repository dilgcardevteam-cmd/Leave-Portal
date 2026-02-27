<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leave_categories', function (Blueprint $table) {
            $table->decimal('default_credits', 8, 3)->default(0)->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('leave_categories', function (Blueprint $table) {
            $table->dropColumn('default_credits');
        });
    }
};

