<?php

namespace Tests\Feature;

use App\Models\Leave;
use App\Models\LeaveCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PdfSinglePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_generated_pdf_is_single_page(): void
    {
        $user = User::factory()->create(['role' => 'user', 'name' => 'Single Page User']);
        $category = LeaveCategory::create(['name' => 'Vacation Leave']);
        $leave = Leave::create([
            'user_id' => $user->id,
            'leave_category_id' => $category->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'days' => 8,
            'status' => 'approved',
            'reason' => 'Details of Leave: Vacation/Special Privilege Leave: Within the Philippines: Baguio; Sick Leave: In Hospital: none; Special Leave Benefits for Women: ; Study Leave: Completion of Master\'s Degree; Other Purpose: Monetization of Leave Credits; Number of Working Days Applied For: 8; Inclusive Dates: '.now()->toDateString().' to '.now()->addDays(7)->toDateString().'; Commutation: Not Requested',
            'details_json' => [
                'working_days' => ['applied_days' => 8, 'inclusive_dates' => now()->toDateString().' to '.now()->addDays(7)->toDateString()],
                'commutation' => 'not_requested',
            ],
        ]);

        $this->actingAs($user);
        $response = $this->get(route('leaves.pdf.view', $leave));
        $response->assertOk();

        $binary = $response->baseResponse;
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\BinaryFileResponse::class, $binary);
        $actualPath = $binary->getFile()->getRealPath();
        $this->assertTrue(file_exists($actualPath));
        $bytes = file_get_contents($actualPath);
        $this->assertNotEmpty($bytes);

        $this->assertStringContainsString('/Type /Pages', $bytes);
        $this->assertTrue(
            strpos($bytes, '/Count 1') !== false || strpos($bytes, '/Count 1 ') !== false,
            'PDF is not single page'
        );
    }
}

