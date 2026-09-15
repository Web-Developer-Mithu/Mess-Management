<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Member;
use App\Models\Mess;
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

        $this->withHeader('X-Forwarded-For', '203.0.113.20')
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
        ]);
    }
}
