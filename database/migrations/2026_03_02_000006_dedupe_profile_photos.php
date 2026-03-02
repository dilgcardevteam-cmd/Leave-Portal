<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;

return new class extends Migration {
    public function up(): void
    {
        // Ensure column exists (in case migration order differs)
        if (!Schema::hasColumn('users', 'photo_path')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('photo_path')->nullable()->after('signature_path');
            });
        }

        // Deduplicate existing files: keep most recent by name pattern user-{id}-timestamp.ext
        $dir = public_path('profile_photos');
        if (is_dir($dir)) {
            /** @var \Illuminate\Support\Collection<int,User> $users */
            $users = User::select(['id','photo_path','updated_at'])->get();
            foreach ($users as $user) {
                $pattern = sprintf('%s/user-%d-*', $dir, $user->id);
                $files = glob($pattern);
                if (!$files) {
                    // No files found; skip
                    continue;
                }
                // Choose most recent by filesystem mtime; fallback to last alphabetically
                usort($files, function ($a, $b) { return filemtime($b) <=> filemtime($a); });
                $keep = $files[0];
                $keepRel = 'profile_photos/'.basename($keep);
                // Update the single source-of-truth column
                DB::table('users')->where('id', $user->id)->update(['photo_path' => $keepRel]);
                // Remove extras
                foreach (array_slice($files, 1) as $extra) {
                    @unlink($extra);
                }
            }
        }
    }

    public function down(): void
    {
        // No-op: do not attempt to restore deleted files
    }
};
