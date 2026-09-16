<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\Meal;
use App\Models\Member;
use App\Models\Mess;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_monthly_report_is_calculated_correctly(): void
    {
        $mess = Mess::create([
            'name' => 'Test Mess',
            'address' => 'Dhaka',
            'balance_alert_threshold' => 500,
            'balance_alert_comment' => 'Please settle your balance.',
        ]);

        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('secret123'),
            'role' => User::ROLE_MESS_ADMIN,
            'mess_id' => $mess->id,
        ]);

        $member = Member::create([
            'mess_id' => $mess->id,
            'name' => 'Md. Mithu',
            'phone' => '01700000000',
            'status' => 'active',
            'join_date' => '2026-01-01',
        ]);

        Member::create([
            'mess_id' => $mess->id,
            'name' => 'Shafi',
            'phone' => '01800000000',
            'status' => 'active',
            'join_date' => '2026-01-02',
        ]);

        $inactiveMember = Member::create([
            'mess_id' => $mess->id,
            'name' => 'Inactive Member',
            'phone' => '01900000000',
            'status' => 'inactive',
            'join_date' => '2026-01-03',
        ]);

        Meal::create(['mess_id' => $mess->id, 'member_id' => $member->id, 'date' => '2026-07-05', 'meal_count' => 2]);
        Meal::create(['mess_id' => $mess->id, 'member_id' => $member->id, 'date' => '2026-07-06', 'meal_count' => 1]);

        Expense::create(['mess_id' => $mess->id, 'date' => '2026-07-10', 'category' => 'Rice', 'amount' => 3000, 'note' => 'Rice bag']);
        Expense::create(['mess_id' => $mess->id, 'date' => '2026-07-12', 'category' => 'Fish', 'amount' => 2000, 'note' => 'Fish supply']);

        Payment::create(['mess_id' => $mess->id, 'member_id' => $member->id, 'date' => '2026-07-11', 'amount' => 1200, 'payment_type' => 'cash']);
        Payment::create(['mess_id' => $mess->id, 'member_id' => $member->id, 'date' => '2026-06-11', 'amount' => 1000, 'payment_type' => 'cash']);
        Meal::create(['mess_id' => $mess->id, 'member_id' => $inactiveMember->id, 'date' => '2026-07-07', 'meal_count' => 10]);
        Payment::create(['mess_id' => $mess->id, 'member_id' => $inactiveMember->id, 'date' => '2026-07-08', 'amount' => 9000, 'payment_type' => 'cash']);

        $this->actingAs($user);

        $response = $this->get(route('dashboard', ['month' => '2026-07']));

        $response->assertOk();
        $response->assertSee('Mess Manager');
        $response->assertSee('Total Meals');
        $response->assertSee('5');
        $response->assertSee('Please settle your balance.');
        $response->assertSee('1,000.00');
        $response->assertSee('Inactive Member');
        $this->assertSame(1, Setting::where('mess_id', $mess->id)->where('month', '2026-07')->count());
    }
}
