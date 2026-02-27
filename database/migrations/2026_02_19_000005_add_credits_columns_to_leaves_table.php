<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->decimal('vl_total', 8, 3)->nullable()->after('reason');
            $table->decimal('vl_less', 8, 3)->nullable()->after('vl_total');
            $table->decimal('vl_balance', 8, 3)->nullable()->after('vl_less');
            $table->decimal('sl_total', 8, 3)->nullable()->after('vl_balance');
            $table->decimal('sl_less', 8, 3)->nullable()->after('sl_total');
            $table->decimal('sl_balance', 8, 3)->nullable()->after('sl_less');
            $table->foreignId('credits_updated_by')->nullable()->after('sl_balance')->constrained('users')->nullOnDelete();
            $table->timestamp('credits_updated_at')->nullable()->after('credits_updated_by');
        });

        // Backfill from details_json if present
        $rows = DB::table('leaves')->select('id', 'details_json')->get();
        foreach ($rows as $row) {
            if (!$row->details_json) continue;
            $json = @json_decode($row->details_json, true);
            if (!is_array($json)) continue;
            $c = $json['credits'] ?? null;
            if (!$c || !is_array($c)) continue;
            $v = $c['vacation'] ?? [];
            $s = $c['sick'] ?? [];
            $m = $c['meta'] ?? [];
            DB::table('leaves')->where('id', $row->id)->update([
                'vl_total' => isset($v['total']) ? (float)$v['total'] : null,
                'vl_less' => isset($v['less']) ? (float)$v['less'] : null,
                'vl_balance' => isset($v['balance']) ? (float)$v['balance'] : null,
                'sl_total' => isset($s['total']) ? (float)$s['total'] : null,
                'sl_less' => isset($s['less']) ? (float)$s['less'] : null,
                'sl_balance' => isset($s['balance']) ? (float)$s['balance'] : null,
                'credits_updated_by' => $m['updated_by_id'] ?? null,
                'credits_updated_at' => isset($m['updated_at']) ? \Carbon\Carbon::parse($m['updated_at']) : null,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            if (Schema::hasColumn('leaves', 'credits_updated_at')) $table->dropColumn('credits_updated_at');
            if (Schema::hasColumn('leaves', 'credits_updated_by')) $table->dropConstrainedForeignId('credits_updated_by');
            if (Schema::hasColumn('leaves', 'sl_balance')) $table->dropColumn('sl_balance');
            if (Schema::hasColumn('leaves', 'sl_less')) $table->dropColumn('sl_less');
            if (Schema::hasColumn('leaves', 'sl_total')) $table->dropColumn('sl_total');
            if (Schema::hasColumn('leaves', 'vl_balance')) $table->dropColumn('vl_balance');
            if (Schema::hasColumn('leaves', 'vl_less')) $table->dropColumn('vl_less');
            if (Schema::hasColumn('leaves', 'vl_total')) $table->dropColumn('vl_total');
        });
    }
};

