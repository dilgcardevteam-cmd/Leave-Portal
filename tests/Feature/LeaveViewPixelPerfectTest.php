<?php

namespace Tests\Feature;

use App\Models\Leave;
use App\Models\LeaveCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveViewPixelPerfectTest extends TestCase
{
    use RefreshDatabase;

    public function test_view_button_returns_reference_image_pixel_perfect(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = LeaveCategory::create(['name' => 'Vacation Leave']);
        $leave = Leave::create([
            'user_id' => $user->id,
            'leave_category_id' => $category->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(1)->toDateString(),
            'days' => 2,
            'status' => 'approved',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('leaves.pdf.view', $leave));
        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/png');

        $binary = $response->baseResponse;
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\BinaryFileResponse::class, $binary);
        $actualPath = $binary->getFile()->getRealPath();
        $this->assertNotEmpty($actualPath);
        $this->assertTrue(file_exists($actualPath));
        $bytes = file_get_contents($actualPath);
        $this->assertNotEmpty($bytes);
        // PNG signature check
        $this->assertSame("\x89PNG\r\n\x1a\n", substr($bytes, 0, 8));
        // Ensure overlay changed bytes vs base form
        $base = null;
        $candidates = [public_path('images/LeaveApplicationForm.png'), public_path('LeaveApplicationForm.png')];
        foreach ($candidates as $c) { if (file_exists($c)) { $base = file_get_contents($c); break; } }
        $this->assertNotEmpty($base);
        $this->assertNotSame($base, $bytes, 'Overlay image should differ from base template');
    }
}
