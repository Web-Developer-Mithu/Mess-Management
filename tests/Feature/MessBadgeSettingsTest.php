<?php

namespace Tests\Feature;

use App\Models\Mess;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class MessBadgeSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_update_mess_name_and_logo_via_badge_setup(): void
    {
        $mess = Mess::create([
            'name' => 'Old Mess Name',
            'address' => 'Dhaka',
            'status' => 'active',
        ]);

        $user = User::create([
            'name' => 'Mess Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('secret123'),
            'role' => User::ROLE_MESS_ADMIN,
            'mess_id' => $mess->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('mess.settings'));
        $response->assertOk();
        $response->assertSee('Badge Setup');

        $response = $this->from(route('mess.settings'))->put(route('mess.settings.update'), [
            'name' => 'New Mess Badge',
            'logo' => UploadedFile::fake()->image('logo.png', 200, 200),
        ]);

        $response->assertRedirect(route('dashboard'));

        $mess->refresh();
        $this->assertSame('New Mess Badge', $mess->name);
        $this->assertNotNull($mess->logo);
        $this->assertStringContainsString('mess-logos', $mess->logo);
    }
}
