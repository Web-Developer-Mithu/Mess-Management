<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\Member;
use App\Models\Mess;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SharedCostAllocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_shared_cost_requires_exact_member_amount_total(): void
    {
        $mess = Mess::create(['name' => 'Shared Cost Mess', 'address' => 'Dhaka']);
        $user = User::create([
            'name' => 'Manager',
            'email' => 'shared-cost@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_MESS_ADMIN,
            'mess_id' => $mess->id,
        ]);
        $first = Member::create(['mess_id' => $mess->id, 'name' => 'First Member', 'status' => 'active']);
        $second = Member::create(['mess_id' => $mess->id, 'name' => 'Second Member', 'status' => 'active']);

        $this->actingAs($user)->post(route('expenses.store'), [
            'date' => '2026-09-16',
            'category' => 'Rent',
            'amount' => 500,
            'type' => 'fixed',
            'member_ids' => [$first->id, $second->id],
            'member_shares' => [$first->id => 300, $second->id => 200],
        ])->assertRedirect(route('dashboard', ['month' => '2026-09']));

        $expense = Expense::firstOrFail();
        $this->assertSame(300.0, (float) $expense->member_shares[$first->id]);
        $this->assertSame(200.0, (float) $expense->member_shares[$second->id]);

        $this->actingAs($user)->post(route('expenses.store'), [
            'date' => '2026-09-17',
            'category' => 'Utility',
            'amount' => 500,
            'type' => 'fixed',
            'member_ids' => [$first->id, $second->id],
            'member_shares' => [$first->id => 300, $second->id => 100],
        ])->assertSessionHasErrors('member_shares');

        $this->assertSame(1, Expense::count());
    }
}
