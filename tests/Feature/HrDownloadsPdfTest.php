<?php

namespace Tests\Feature;

use App\Models\Leave;
use App\Models\LeaveCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HrDownloadsPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_hr_downloads_page_and_pdf_layout(): void
    {
        $hr = User::factory()->create(['role' => 'hr']);
        $user = User::factory()->create(['role' => 'user', 'name' => 'Juan Dela Cruz']);
        $category = LeaveCategory::create(['name' => 'Vacation Leave']);
        $leave = Leave::create([
            'user_id' => $user->id,
            'leave_category_id' => $category->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(1)->toDateString(),
            'days' => 2,
            'status' => 'approved',
        ]);

        $this->actingAs($hr);

        $page = $this->get(route('hr.downloads'));
        $page->assertOk();
        $page->assertSee('Approved Requests Downloads');
        $page->assertSee($user->name);
        $page->assertSee('Download');

        $response = $this->get(route('leaves.pdf', $leave));
        $response->assertOk();

        $binary = $response->baseResponse;
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\BinaryFileResponse::class, $binary);
        $actualPath = $binary->getFile()->getRealPath();
        $this->assertNotEmpty($actualPath);
        $this->assertTrue(file_exists($actualPath));

        $html = view('leaves.pdf', ['leave' => $leave])->render();
        $this->assertStringContainsString('APPLICATION FOR LEAVE', $html);
        $this->assertTrue(str_contains($html, mb_strtoupper($user->name)) || str_contains($html, $user->name));
        $this->assertStringContainsString('Vacation Leave', $html);
    }
}

