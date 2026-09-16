<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Expense;
use App\Models\Member;
use App\Models\Mess;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExpenseCrudRecoveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_expense_can_be_listed_edited_and_deleted(): void
    {
        $mess = Mess::create(['name' => 'Expense Mess', 'address' => 'Dhaka']);
        $user = User::create([
            'name' => 'Expense Admin',
            'email' => 'expenses@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_MESS_ADMIN,
            'mess_id' => $mess->id,
        ]);
        $member = Member::create(['mess_id' => $mess->id, 'name' => 'Expense Member', 'status' => 'active']);

        $this->actingAs($user)
            ->post(route('expenses.store'), [
                'date' => '2026-09-16',
                'category' => 'Rice',
                'amount' => 500,
                'vendor' => 'Local Market',
                'note' => 'First batch',
                'type' => 'fixed',
                'member_ids' => [$member->id],
                'member_shares' => [$member->id => 500],
            ])
            ->assertRedirect(route('dashboard', ['month' => '2026-09']));

        $expense = Expense::firstOrFail();

        $this->actingAs($user)
            ->get(route('expenses.index'))
            ->assertOk()
            ->assertSee('Rice')
            ->assertSee('Edit')
            ->assertSee('Delete');

        $this->actingAs($user)
            ->put(route('expenses.update', $expense), [
                'date' => '2026-09-17',
                'category' => 'Rice & Oil',
                'amount' => 650,
                'vendor' => 'Updated Market',
                'note' => 'Adjusted',
                'type' => 'fixed',
                'member_ids' => [$member->id],
                'member_shares' => [$member->id => 650],
            ])
            ->assertRedirect(route('expenses.index'));

        $this->assertDatabaseHas('expenses', ['id' => $expense->id, 'category' => 'Rice & Oil', 'amount' => 650.00]);

        $this->actingAs($user)
            ->delete(route('expenses.destroy', $expense))
            ->assertRedirect(route('expenses.index'));

        $this->assertSame(0, Expense::count());
    }

    public function test_super_admin_can_restore_deleted_expense_from_activity_log(): void
    {
        $mess = Mess::create(['name' => 'Restore Mess', 'address' => 'Dhaka']);
        $user = User::create([
            'name' => 'Restore Admin',
            'email' => 'restore@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_MESS_ADMIN,
            'mess_id' => $mess->id,
        ]);
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superrestore@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
        $member = Member::create(['mess_id' => $mess->id, 'name' => 'Restore Member', 'status' => 'active']);

        $this->actingAs($user)->post(route('expenses.store'), [
            'date' => '2026-09-18',
            'category' => 'Milk',
            'amount' => 300,
            'type' => 'fixed',
            'member_ids' => [$member->id],
            'member_shares' => [$member->id => 300],
        ]);

        $expense = Expense::firstOrFail();
        $this->actingAs($user)->delete(route('expenses.destroy', $expense));

        $log = ActivityLog::where('model_type', Expense::class)
            ->where('action', 'deleted')
            ->where('model_id', $expense->id)
            ->latest('id')
            ->firstOrFail();

        $this->actingAs($superAdmin)
            ->post(route('superadmin.activity.logs.restore', $log))
            ->assertRedirect(route('superadmin.activity.logs'));

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'category' => 'Milk',
            'amount' => 300.00,
        ]);
    }
}
