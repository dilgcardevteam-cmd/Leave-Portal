<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_single_source_of_truth_column_exists(): void
    {
        $user = User::factory()->create();
        $this->assertTrue(\Schema::hasColumn('users', 'photo_path'));
        $this->assertNull($user->photo_path);
    }

    public function test_upload_sets_only_one_avatar_and_deletes_old(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $first = UploadedFile::fake()->image('first.jpg', 100, 100);
        $this->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'photo' => $first,
        ])->assertRedirect('/profile');

        $user->refresh();
        $this->assertNotNull($user->photo_path);
        $firstAbs = public_path($user->photo_path);
        $this->assertTrue(is_file($firstAbs));

        $second = UploadedFile::fake()->image('second.png', 120, 120);
        $this->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'photo' => $second,
        ])->assertRedirect('/profile');

        $user->refresh();
        $this->assertNotNull($user->photo_path);
        $this->assertTrue(is_file(public_path($user->photo_path)));
        $this->assertFalse(is_file($firstAbs), 'Old avatar should be deleted to prevent duplicates');
    }

    public function test_ui_surfaces_use_same_avatar_url(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $file = UploadedFile::fake()->image('a.png', 80, 80);
        $this->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'photo' => $file,
        ])->assertRedirect('/profile');
        $user->refresh();
        $url = asset($user->photo_path).'?v='.$user->updated_at->getTimestamp();

        // Profile page shows preview
        $profile = $this->get('/profile')->getContent();
        $this->assertStringContainsString($url, $profile);

        // Navigation layout is included on dashboard
        $dashboard = $this->get('/dashboard')->getContent();
        $this->assertStringContainsString($url, $dashboard);
    }
}
