<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vl_sl_earnings', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('days')->unique();
            $table->decimal('vacation_leave_earned', 6, 3)->default(0);
            $table->decimal('sick_leave_earned', 6, 3)->default(0);
            $table->timestamps();
        });

        $now = now();
        DB::table('vl_sl_earnings')->insert([
            ['days' => 1, 'vacation_leave_earned' => 0.042, 'sick_leave_earned' => 0.042, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 2, 'vacation_leave_earned' => 0.083, 'sick_leave_earned' => 0.083, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 3, 'vacation_leave_earned' => 0.125, 'sick_leave_earned' => 0.125, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 4, 'vacation_leave_earned' => 0.167, 'sick_leave_earned' => 0.167, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 5, 'vacation_leave_earned' => 0.208, 'sick_leave_earned' => 0.208, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 6, 'vacation_leave_earned' => 0.250, 'sick_leave_earned' => 0.250, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 7, 'vacation_leave_earned' => 0.292, 'sick_leave_earned' => 0.292, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 8, 'vacation_leave_earned' => 0.333, 'sick_leave_earned' => 0.333, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 9, 'vacation_leave_earned' => 0.375, 'sick_leave_earned' => 0.375, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 10, 'vacation_leave_earned' => 0.417, 'sick_leave_earned' => 0.417, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 11, 'vacation_leave_earned' => 0.458, 'sick_leave_earned' => 0.458, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 12, 'vacation_leave_earned' => 0.500, 'sick_leave_earned' => 0.500, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 13, 'vacation_leave_earned' => 0.542, 'sick_leave_earned' => 0.542, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 14, 'vacation_leave_earned' => 0.583, 'sick_leave_earned' => 0.583, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 15, 'vacation_leave_earned' => 0.625, 'sick_leave_earned' => 0.625, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 16, 'vacation_leave_earned' => 0.667, 'sick_leave_earned' => 0.667, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 17, 'vacation_leave_earned' => 0.708, 'sick_leave_earned' => 0.708, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 18, 'vacation_leave_earned' => 0.750, 'sick_leave_earned' => 0.750, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 19, 'vacation_leave_earned' => 0.792, 'sick_leave_earned' => 0.792, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 20, 'vacation_leave_earned' => 0.833, 'sick_leave_earned' => 0.833, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 21, 'vacation_leave_earned' => 0.875, 'sick_leave_earned' => 0.875, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 22, 'vacation_leave_earned' => 0.917, 'sick_leave_earned' => 0.917, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 23, 'vacation_leave_earned' => 0.958, 'sick_leave_earned' => 0.958, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 24, 'vacation_leave_earned' => 1.000, 'sick_leave_earned' => 1.000, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 25, 'vacation_leave_earned' => 1.042, 'sick_leave_earned' => 1.042, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 26, 'vacation_leave_earned' => 1.083, 'sick_leave_earned' => 1.083, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 27, 'vacation_leave_earned' => 1.125, 'sick_leave_earned' => 1.125, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 28, 'vacation_leave_earned' => 1.167, 'sick_leave_earned' => 1.167, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 29, 'vacation_leave_earned' => 1.208, 'sick_leave_earned' => 1.208, 'created_at' => $now, 'updated_at' => $now],
            ['days' => 30, 'vacation_leave_earned' => 1.250, 'sick_leave_earned' => 1.250, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('vl_sl_earnings');
    }
};
