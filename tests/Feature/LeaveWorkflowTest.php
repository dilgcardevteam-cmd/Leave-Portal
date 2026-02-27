<?php

namespace Tests\Feature;

use App\Models\LeaveCategory;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LeaveWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_multistage_approval_flow_generates_pdf(): void
    {
        Storage::fake('local'); // isolate filesystem side effects

        $employee = User::factory()->create(['role' => 'user']);
        $hr = User::factory()->create(['role' => 'hr']);
        $dc = User::factory()->create(['role' => 'dc']);
        $rd = User::factory()->create(['role' => 'rd']);
        $category = LeaveCategory::factory()->create(['name' => 'Vacation Leave']);

        $this->actingAs($employee)->post(route('leaves.store'), [
            'leave_category_id' => $category->id,
            'start_date' => '2026-02-18',
            'end_date' => '2026-02-19',
        ])->assertRedirect(route('leaves.index', absolute: false));

        $leave = Leave::firstOrFail();
        $this->assertSame('hr_pending', $leave->workflow_state);
        $this->assertSame('pending', $leave->status);

        $this->actingAs($hr)->post(route('hr.leaves.status', $leave), [
            'status' => 'approved',
            'comment' => 'Credits checked',
        ])->assertSessionHasNoErrors();

        $leave->refresh();
        $this->assertSame('dc_pending', $leave->workflow_state);
        $this->assertNotNull($leave->hr_approved_by);
        $this->assertNotNull($leave->hr_approved_at);

        $this->actingAs($dc)->post(route('dc.leaves.status', $leave), [
            'status' => 'approved',
            'comment' => 'Proceed to final',
        ])->assertSessionHasNoErrors();

        $leave->refresh();
        $this->assertSame('final_pending', $leave->workflow_state);
        $this->assertNotNull($leave->dc_approved_by);
        $this->assertNotNull($leave->dc_approved_at);

        $this->actingAs($rd)->post(route('rd.leaves.status', $leave), [
            'status' => 'approved',
            'comment' => 'Approved',
        ])->assertSessionHasNoErrors();

        $leave->refresh();
        $this->assertSame('approved', $leave->status);
        $this->assertSame('approved', $leave->workflow_state);
        $this->assertSame('rd', $leave->final_approver_role);
        $this->assertNotNull($leave->final_pdf_path);
    }
}

