<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\Meal;
use App\Models\Member;
use App\Models\Mess;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_weekly_and_monthly_reports_are_available(): void
    {
        $mess = Mess::create(['name' => 'Report Mess', 'address' => 'Dhaka']);
        $user = User::create([
            'name' => 'Report Manager',
            'email' => 'reports@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_MESS_ADMIN,
            'mess_id' => $mess->id,
        ]);
        $member = Member::create(['mess_id' => $mess->id, 'name' => 'Report Member', 'status' => 'active']);
        $inactiveMember = Member::create(['mess_id' => $mess->id, 'name' => 'Inactive Report Member', 'status' => 'inactive']);
        Meal::create(['mess_id' => $mess->id, 'member_id' => $member->id, 'date' => '2026-09-16', 'meal_count' => 2]);
        Meal::create(['mess_id' => $mess->id, 'member_id' => $inactiveMember->id, 'date' => '2026-09-16', 'meal_count' => 1]);
        Expense::create(['mess_id' => $mess->id, 'date' => '2026-09-16', 'category' => 'Rice', 'amount' => 300]);
        Expense::create([
            'mess_id' => $mess->id,
            'date' => '2026-09-16',
            'category' => 'Rent',
            'amount' => 500,
            'type' => 'fixed',
            'is_fixed' => true,
            'member_ids' => [$member->id],
            'member_shares' => [$member->id => 500],
        ]);
        Payment::create(['mess_id' => $mess->id, 'member_id' => $member->id, 'date' => '2026-09-16', 'amount' => 100, 'payment_type' => 'cash']);

        foreach ([
            ['type' => 'daily', 'date' => '2026-09-16'],
            ['type' => 'weekly', 'week_start' => '2026-09-14'],
            ['type' => 'monthly', 'month' => '2026-09'],
        ] as $query) {
            $this->actingAs($user)->get(route('reports.index', $query))
                ->assertOk()
                ->assertSee('Member-wise Report')
                ->assertSee('Report Member')
                ->assertSee('Inactive Report Member')
                ->assertSee('Inactive')
                ->assertSee('Shared Cost Report')
                ->assertSee('Rent')
                ->assertSee('500.00')
                ->assertSee('Merged Cost Report')
                ->assertSee('Combined Total Cost')
                ->assertSee('Total Cost');
        }
    }
}
