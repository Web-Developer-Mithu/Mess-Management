<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_update_developer_branding_from_settings(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('secret123'),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $this->actingAs($superAdmin);

        $this->get(route('superadmin.site.settings'))->assertOk();

        $this->put(route('superadmin.site.settings.update'), [
            'brand_name' => 'Mess Manager Pro',
            'brand_logo' => UploadedFile::fake()->image('brand-logo.png', 600, 600),
            'developer_name' => 'Md. Mithu Rahman',
            'developer_tagline' => 'Smart Soft X InterX',
            'phone' => '01875960149',
            'facebook_url' => 'https://facebook.com/example',
            'whatsapp_url' => 'https://wa.me/8801875960149',
            'avatar' => UploadedFile::fake()->image('developer.png', 400, 400),
        ])->assertRedirect(route('superadmin.dashboard'));

        $setting = SiteSetting::query()->first();

        $this->assertNotNull($setting);
        $this->assertSame('Mess Manager Pro', $setting->brand_name);
        $this->assertSame('Md. Mithu Rahman', $setting->developer_name);
        $this->assertSame('01875960149', $setting->phone);
        $this->assertNotNull($setting->brand_logo_path);
        $this->assertStringContainsString('brand-logo', $setting->brand_logo_path);
        $this->assertNotNull($setting->avatar_path);
        $this->assertStringContainsString('developer', $setting->avatar_path);

        $this->get(route('dashboard'))->assertSee('Md. Mithu Rahman');
    }
}
