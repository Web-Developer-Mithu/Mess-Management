<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Member;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SuperAdminFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_login_and_see_all_messes(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->post('/login', [
            'email' => 'mdmithurahman40@gmail.com',
            'password' => 'Mithu@1713183575',
        ]);

        $response->assertRedirect('/superadmin');
        $this->assertAuthenticatedAs(User::where('email', 'mdmithurahman40@gmail.com')->first());

        $this->get('/superadmin')
            ->assertOk()
            ->assertSeeText('Active Messes');
    }

    public function test_super_admin_can_update_mess_name_and_logo(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::where('email', 'mdmithurahman40@gmail.com')->firstOrFail();
        $mess = \App\Models\Mess::create([
            'name' => 'Old Mess Name',
            'address' => 'Dhaka',
        ]);

        $this->actingAs($superAdmin)
            ->put(route('superadmin.messes.update', $mess), [
                'name' => 'New Mess Name',
                'address' => 'Chattogram',
                'logo' => UploadedFile::fake()->image('logo.png'),
                'balance_alert_threshold' => '500.00',
                'balance_alert_comment' => 'Please settle this balance this week.',
            ])
            ->assertRedirect(route('superadmin.dashboard'));

        $mess->refresh();

        $this->assertSame('New Mess Name', $mess->name);
        $this->assertSame('Chattogram', $mess->address);
        $this->assertNotNull($mess->logo);
        $this->assertSame('500.00', $mess->balance_alert_threshold);
        $this->assertSame('Please settle this balance this week.', $mess->balance_alert_comment);
        Storage::disk('public')->assertExists($mess->logo);
    }

    public function test_super_admin_can_deactivate_and_reactivate_mess_without_deleting_data(): void
    {
        $this->seed(DatabaseSeeder::class);
        $superAdmin = User::where('email', 'mdmithurahman40@gmail.com')->firstOrFail();
        $mess = \App\Models\Mess::create(['name' => 'Status Mess', 'address' => 'Dhaka']);
        $member = Member::create(['mess_id' => $mess->id, 'name' => 'Kept Member', 'status' => 'active']);

        $this->actingAs($superAdmin)
            ->post(route('superadmin.messes.deactivate', $mess))
            ->assertRedirect(route('superadmin.dashboard'));

        $this->assertDatabaseHas('messes', ['id' => $mess->id, 'status' => 'inactive']);
        $this->assertDatabaseHas('members', ['id' => $member->id, 'name' => 'Kept Member']);

        $this->actingAs($superAdmin)
            ->post(route('superadmin.messes.activate', $mess))
            ->assertRedirect(route('superadmin.dashboard'));

        $this->assertDatabaseHas('messes', ['id' => $mess->id, 'status' => 'active']);
    }

    public function test_manager_cannot_login_while_mess_is_inactive(): void
    {
        $mess = \App\Models\Mess::create([
            'name' => 'Inactive Login Mess',
            'status' => 'inactive',
            'inactive_message' => 'Mess বন্ধ আছে, Super Admin activate না করা পর্যন্ত কাজ হবে না।',
        ]);
        User::create([
            'name' => 'Inactive Manager',
            'email' => 'inactive-manager@example.com',
            'password' => bcrypt('password123'),
            'role' => User::ROLE_MESS_ADMIN,
            'mess_id' => $mess->id,
        ]);

        $this->post('/login', [
            'email' => 'inactive-manager@example.com',
            'password' => 'password123',
        ])->assertSessionHasErrors('email')
            ->assertSessionHasErrors(['email' => 'Mess বন্ধ আছে, Super Admin activate না করা পর্যন্ত কাজ হবে না।']);

        $this->assertGuest();
    }

    public function test_impersonation_shows_one_time_dashboard_popup(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'popup-superadmin@example.com',
            'password' => bcrypt('password123'),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
        $mess = \App\Models\Mess::create(['name' => 'Popup Mess', 'address' => 'Dhaka']);
        $manager = User::create([
            'name' => 'Popup Manager',
            'email' => 'popup-manager@example.com',
            'password' => bcrypt('password123'),
            'role' => User::ROLE_MESS_ADMIN,
            'mess_id' => $mess->id,
        ]);

        $this->actingAs($superAdmin)
            ->post(route('superadmin.impersonate', $mess->id))
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($manager);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Popup Mess')
            ->assertSee('Mess-এ প্রবেশ করেছেন');

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Mess-এ প্রবেশ করেছেন');
    }
}
