<?php

namespace Tests\Feature;

use App\Models\Meal;
use App\Models\Member;
use App\Models\Mess;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BulkMealEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_meals_can_be_added_for_all_active_members_on_a_date(): void
    {
        $mess = Mess::create(['name' => 'Bulk Mess', 'address' => 'Dhaka']);
        $user = User::create([
            'name' => 'Manager',
            'email' => 'bulk@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_MESS_ADMIN,
            'mess_id' => $mess->id,
        ]);

        $first = Member::create([
            'mess_id' => $mess->id,
            'name' => 'Active One',
            'status' => 'active',
        ]);
        $second = Member::create([
            'mess_id' => $mess->id,
            'name' => 'Active Two',
            'status' => 'active',
        ]);
        Member::create([
            'mess_id' => $mess->id,
            'name' => 'Inactive One',
            'status' => 'inactive',
        ]);

        $this->actingAs($user)
            ->post(route('meals.bulk.store'), [
                'date' => '2026-09-16',
                'meal_counts' => [$first->id => 2.5, $second->id => 1.25],
                'note' => 'Lunch',
            ])
            ->assertRedirect(route('dashboard', ['month' => '2026-09']));

        $this->assertSame(2, Meal::where('date', '2026-09-16')->count());
        $this->assertSame('2.50', Meal::where('date', '2026-09-16')->where('member_id', $first->id)->value('meal_count'));
        $this->assertSame('1.25', Meal::where('date', '2026-09-16')->where('member_id', $second->id)->value('meal_count'));

        $this->actingAs($user)->post(route('meals.bulk.store'), [
            'date' => '2026-09-16',
            'meal_counts' => [$first->id => 3.25, $second->id => 2.75],
        ]);

        $this->assertSame(2, Meal::where('date', '2026-09-16')->count());
        $this->assertSame('3.25', Meal::where('date', '2026-09-16')->where('member_id', $first->id)->value('meal_count'));
        $this->assertSame('2.75', Meal::where('date', '2026-09-16')->where('member_id', $second->id)->value('meal_count'));
    }
}
