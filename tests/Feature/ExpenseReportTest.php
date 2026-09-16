<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\Member;
use App\Models\Mess;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExpenseReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_monthly_weekly_and_daily_expense_reports_are_visible_and_editable(): void
    {
        $mess = Mess::create(['name' => 'Report Mess', 'address' => 'Dhaka']);
        $user = User::create([
            'name' => 'Report Admin',
            'email' => 'report@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_MESS_ADMIN,
            'mess_id' => $mess->id,
        ]);
        $member = Member::create(['mess_id' => $mess->id, 'name' => 'Report Member', 'status' => 'active']);

        $expense = Expense::create([
            'mess_id' => $mess->id,
            'date' => '2026-09-16',
            'category' => 'Rice',
            'amount' => 500,
            'vendor' => 'Local Market',
            'note' => 'In bulk',
            'type' => 'fixed',
            'member_ids' => [$member->id],
            'member_shares' => [$member->id => 500],
        ]);

        $this->actingAs($user)
            ->get(route('reports.index', ['type' => 'monthly', 'month' => '2026-09']))
            ->assertOk()
            ->assertSee('Rice')
            ->assertSee('Edit')
            ->assertSee('Delete');

        $this->actingAs($user)
            ->get(route('reports.index', ['type' => 'weekly', 'week_start' => '2026-09-14']))
            ->assertOk()
            ->assertSee('Rice');

        $this->actingAs($user)
            ->get(route('reports.index', ['type' => 'daily', 'date' => '2026-09-16']))
            ->assertOk()
            ->assertSee('Rice');

        $this->actingAs($user)
            ->get(route('reports.expenses', ['type' => 'monthly', 'month' => '2026-09']))
            ->assertOk()
            ->assertSee('Expense Report')
            ->assertSee('Rice')
            ->assertSee('Edit')
            ->assertSee('Delete');

        $this->assertDatabaseHas('expenses', ['id' => $expense->id, 'category' => 'Rice']);
    }
}
