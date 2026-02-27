<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->after('id');
            }
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->after('username');
            }
            if (!Schema::hasColumn('users', 'middle_name')) {
                $table->string('middle_name')->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->after('middle_name');
            }
            if (!Schema::hasColumn('users', 'mobile_number')) {
                $table->string('mobile_number')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'sex')) {
                $table->string('sex', 10)->nullable()->after('mobile_number');
            }
            if (!Schema::hasColumn('users', 'region')) {
                $table->string('region')->nullable()->after('sex');
            }
            if (!Schema::hasColumn('users', 'province_office')) {
                $table->string('province_office')->nullable()->after('region');
            }
            if (!Schema::hasColumn('users', 'position')) {
                $table->string('position')->nullable()->after('province_office');
            }
            if (!Schema::hasColumn('users', 'id_no')) {
                $table->string('id_no')->nullable()->unique()->after('position');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user')->after('id_no');
            }
            if (!Schema::hasColumn('users', 'otp_code')) {
                $table->string('otp_code')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'otp_expires_at')) {
                $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $drops = ['username','first_name','middle_name','last_name','mobile_number','sex','region','province_office','position','id_no','role','otp_code','otp_expires_at'];
            foreach ($drops as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
