<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->string('workflow_state')->default('hr_pending')->after('status');
            $table->foreignId('hr_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('hr_approved_at')->nullable();
            $table->text('hr_comment')->nullable();

            $table->foreignId('dc_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dc_approved_at')->nullable();
            $table->text('dc_comment')->nullable();

            $table->foreignId('final_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('final_approved_at')->nullable();
            $table->enum('final_approver_role', ['rd','ard'])->nullable();
            $table->text('final_comment')->nullable();
            $table->string('final_pdf_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn('workflow_state');
            $table->dropConstrainedForeignId('hr_approved_by');
            $table->dropColumn(['hr_approved_at','hr_comment']);
            $table->dropConstrainedForeignId('dc_approved_by');
            $table->dropColumn(['dc_approved_at','dc_comment']);
            $table->dropConstrainedForeignId('final_approved_by');
            $table->dropColumn(['final_approved_at','final_approver_role','final_comment','final_pdf_path']);
        });
    }
};

