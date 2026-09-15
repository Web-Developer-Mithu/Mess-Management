<?php

namespace Tests\Feature;

use App\Models\User;
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
}
