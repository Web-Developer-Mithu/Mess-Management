<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Mess;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MemberManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_edit_page_is_accessible(): void
    {
        $mess = Mess::create([
            'name' => 'Alpha Mess',
            'address' => 'Dhaka',
        ]);

        $user = User::create([
            'name' => 'Mess Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_MESS_ADMIN,
            'mess_id' => $mess->id,
        ]);

        $member = Member::create([
            'name' => 'Rahim',
            'phone' => '01700000000',
            'status' => 'active',
            'join_date' => '2026-09-01',
            'mess_id' => $mess->id,
        ]);

        $this->actingAs($user)
            ->get(route('members.edit', $member))
            ->assertOk()
            ->assertSee('Edit Member')
            ->assertSee('Rahim');
    }

    public function test_member_can_be_updated(): void
    {
        $mess = Mess::create([
            'name' => 'Alpha Mess',
            'address' => 'Chattogram',
        ]);

        $user = User::create([
            'name' => 'Mess Admin',
            'email' => 'admin2@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_MESS_ADMIN,
            'mess_id' => $mess->id,
        ]);

        $member = Member::create([
            'name' => 'Rahim',
            'phone' => '01700000000',
            'status' => 'active',
            'join_date' => '2026-09-01',
            'mess_id' => $mess->id,
        ]);

        $this->actingAs($user)
            ->put(route('members.update', $member), [
                'name' => 'Updated Rahim',
                'phone' => '01811111111',
                'status' => 'inactive',
                'join_date' => '2026-09-10',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'name' => 'Updated Rahim',
            'phone' => '01811111111',
            'status' => 'inactive',
        ]);
    }

    public function test_member_can_be_soft_deleted_and_restored(): void
    {
        $mess = Mess::create([
            'name' => 'Alpha Mess',
            'address' => 'Sylhet',
        ]);

        $user = User::create([
            'name' => 'Mess Admin',
            'email' => 'admin3@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_MESS_ADMIN,
            'mess_id' => $mess->id,
        ]);

        $member = Member::create([
            'name' => 'Karim',
            'phone' => '01900000000',
            'status' => 'active',
            'join_date' => '2026-09-05',
            'mess_id' => $mess->id,
        ]);

        $this->actingAs($user)
            ->delete(route('members.destroy', $member))
            ->assertRedirect(route('dashboard'));

        $this->assertSoftDeleted('members', ['id' => $member->id]);

        $this->actingAs($user)
            ->post(route('members.restore', $member))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('members', ['id' => $member->id, 'deleted_at' => null]);
    }
}
