<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Member;
use App\Models\Mess;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_create_logs_user_ip_and_time(): void
    {
        $mess = Mess::create([
            'name' => 'Alpha Mess',
            'address' => 'Dhaka',
        ]);

        $user = User::create([
            'name' => 'Mess Admin',
            'email' => 'activity@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_MESS_ADMIN,
            'mess_id' => $mess->id,
        ]);

        $this->withHeaders([
            'X-Forwarded-For' => '203.0.113.20',
            'CF-IPCountry' => 'BD',
            'CF-IPCity' => 'Dhaka',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36',
        ])
            ->actingAs($user)
            ->post(route('members.store'), [
                'name' => 'New Member',
                'phone' => '01712345678',
                'status' => 'active',
                'join_date' => '2026-09-16',
            ]);

        $this->assertDatabaseHas('activity_logs', [
            'model_type' => Member::class,
            'action' => 'created',
            'user_id' => $user->id,
            'ip_address' => '203.0.113.20',
            'mess_id' => $mess->id,
            'device_type' => 'Desktop',
            'browser' => 'Google Chrome',
            'platform' => 'Windows',
            'location' => 'Dhaka, BD',
        ]);
    }

    public function test_super_admin_can_filter_activity_by_mess_and_action(): void
    {
        $mess = Mess::create(['name' => 'Filter Mess', 'address' => 'Dhaka']);
        $user = User::create([
            'name' => 'Filter Admin',
            'email' => 'filter@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_MESS_ADMIN,
            'mess_id' => $mess->id,
        ]);
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super-filter@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'mess_id' => $mess->id,
            'model_type' => Member::class,
            'model_id' => 1,
            'action' => 'updated',
            'details' => 'Filtered Member',
        ]);

        $this->actingAs($superAdmin)
            ->get(route('superadmin.activity.logs', ['mess_id' => $mess->id, 'action' => 'updated']))
            ->assertOk()
            ->assertSee('Filtered Member')
            ->assertSee('Filter Mess');
    }

    public function test_member_update_logs_before_and_after_values(): void
    {
        $mess = Mess::create(['name' => 'History Mess', 'address' => 'Dhaka']);
        $user = User::create([
            'name' => 'History Admin',
            'email' => 'history@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_MESS_ADMIN,
            'mess_id' => $mess->id,
        ]);
        $member = Member::create([
            'mess_id' => $mess->id,
            'name' => 'Before Name',
            'phone' => '01700000000',
            'status' => 'active',
        ]);

        $this->actingAs($user)->put(route('members.update', $member), [
            'name' => 'After Name',
            'phone' => '01800000000',
            'status' => 'inactive',
        ]);

        $log = ActivityLog::where('model_type', Member::class)
            ->where('model_id', $member->id)
            ->where('action', 'updated')
            ->latest('id')
            ->firstOrFail();

        $this->assertSame('Before Name', $log->old_values['name']);
        $this->assertSame('After Name', $log->new_values['name']);
        $this->assertSame('inactive', $log->new_values['status']);
    }

    public function test_payment_amount_and_change_history_are_logged(): void
    {
        $mess = Mess::create(['name' => 'Payment History Mess', 'address' => 'Dhaka']);
        $user = User::create([
            'name' => 'Payment Admin',
            'email' => 'payment-history@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_MESS_ADMIN,
            'mess_id' => $mess->id,
        ]);
        $member = Member::create(['mess_id' => $mess->id, 'name' => 'Payment Member', 'status' => 'active']);

        $this->actingAs($user);
        $payment = Payment::create([
            'mess_id' => $mess->id,
            'member_id' => $member->id,
            'date' => '2026-09-16',
            'amount' => 1000,
            'payment_type' => 'cash',
        ]);
        $payment->update(['amount' => 1250]);

        $log = ActivityLog::where('model_type', Payment::class)
            ->where('model_id', $payment->id)
            ->where('action', 'updated')
            ->latest('id')
            ->firstOrFail();

        $this->assertStringContainsString('Payment ৳1,250.00', $log->details);
        $this->assertEquals(1000, (float) $log->old_values['amount']);
        $this->assertEquals(1250, (float) $log->new_values['amount']);
    }
}
