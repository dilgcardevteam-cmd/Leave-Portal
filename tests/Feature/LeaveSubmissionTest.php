<?php

namespace Tests\Feature;

use App\Models\LeaveCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LeaveSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_leave_with_full_details(): void
    {
        $user = User::factory()->create();
        $category = LeaveCategory::factory()->create(['name' => 'Study Leave']);
        Notification::fake();

        $response = $this
            ->actingAs($user)
            ->post(route('leaves.store'), [
                'leave_category_id' => $category->id,
                'start_date' => '2026-02-18',
                'end_date' => '2026-02-20',
                'detail_vac_within' => 'Baguio City',
                'detail_sick_outpatient' => 'Migraine',
                'detail_women' => 'OB checkup',
                'detail_study_master' => true,
                'detail_other_monetization' => true,
                'applied_days' => 3,
                'inclusive_dates_text' => 'Feb 18-20, 2026',
                'commutation' => 'requested',
                'reason' => 'Testing submission',
            ]);

        $response
            ->assertRedirect(route('leaves.index', absolute: false))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status', 'Leave request submitted.');

        $this->assertDatabaseHas('leaves', [
            'user_id' => $user->id,
            'leave_category_id' => $category->id,
            'start_date' => '2026-02-18',
            'end_date' => '2026-02-20',
            'days' => 3,
            'status' => 'pending',
        ]);

        $row = \DB::table('leaves')->where('user_id', $user->id)->first();
        $details = json_decode($row->details_json, true);
        $this->assertSame('Study Leave', $details['type_of_leave']['name']);
        $this->assertSame(3, $details['working_days']['applied_days']);
        $this->assertSame('Feb 18-20, 2026', $details['working_days']['inclusive_dates']);
        $this->assertSame('requested', $details['commutation']);
        $this->assertSame('Baguio City', $details['details_of_leave']['vacation']['within_ph']);
        $this->assertSame('Migraine', $details['details_of_leave']['sick']['outpatient']);
        $this->assertTrue($details['details_of_leave']['study']['master']);
        $this->assertTrue($details['details_of_leave']['other']['monetization']);
    }

    public function test_missing_category_fails_validation(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('leaves.create'))
            ->post(route('leaves.store'), [
                'start_date' => '2026-02-18',
                'end_date' => '2026-02-18',
            ]);

        $response
            ->assertRedirect(route('leaves.create', absolute: false))
            ->assertSessionHasErrors(['leave_category_id']);
    }

    public function test_days_are_computed_when_applied_days_missing(): void
    {
        $user = User::factory()->create();
        $category = LeaveCategory::factory()->create(['name' => 'Vacation Leave']);

        $response = $this
            ->actingAs($user)
            ->post(route('leaves.store'), [
                'leave_category_id' => $category->id,
                'start_date' => '2026-02-18',
                'end_date' => '2026-02-19',
            ]);

        $response->assertRedirect(route('leaves.index', absolute: false));

        $this->assertDatabaseHas('leaves', [
            'user_id' => $user->id,
            'days' => 2,
        ]);

        $row = \DB::table('leaves')->where('user_id', $user->id)->first();
        $details = json_decode($row->details_json, true);
        $this->assertSame(2, $details['working_days']['applied_days']);
    }
}

