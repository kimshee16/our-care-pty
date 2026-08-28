<?php

namespace Tests\Feature;

use App\Models\CmsSetting;
use App\Models\User;
use App\Support\CmsContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsDefaultsResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_cms_defaults_include_configured_services(): void
    {
        $defaults = CmsContent::defaults();

        $this->assertSame(
            config('ourcare_v2.services.personal-care-support.title'),
            $defaults['services']['personal-care-support']['title']
        );
    }

    public function test_admin_cms_page_shows_restore_defaults_controls(): void
    {
        $admin = User::factory()->create(['accounttype' => 'admin']);

        $this->withSession(['user' => $admin->toArray()])
            ->get('/admin/cms')
            ->assertOk()
            ->assertSee('Restore Defaults')
            ->assertSee('cms-reset-page-home-v2', false)
            ->assertSee('cms-reset-service-personal-care-support', false);
    }

    public function test_admin_can_restore_one_section_to_defaults(): void
    {
        $admin = User::factory()->create(['accounttype' => 'admin']);

        CmsContent::set('brand', [
            'site_name' => 'Changed Care',
        ]);

        $this->withSession(['user' => $admin->toArray()])
            ->post('/admin/cms/reset', ['key' => 'brand'])
            ->assertRedirect('/admin/cms#site-kit')
            ->assertSessionHas('status', 'CMS section restored to defaults.');

        $this->assertDatabaseMissing('cms_settings', ['key' => 'brand']);
        $this->assertSame(config('cms.brand.site_name'), CmsContent::get('brand.site_name'));
    }

    public function test_admin_can_restore_all_cms_settings_to_defaults(): void
    {
        $admin = User::factory()->create(['accounttype' => 'admin']);

        CmsContent::set('brand', ['site_name' => 'Changed Care']);
        CmsContent::set('palette', ['primary' => '#000000']);

        $this->withSession(['user' => $admin->toArray()])
            ->post('/admin/cms/reset', ['key' => 'all'])
            ->assertRedirect('/admin/cms')
            ->assertSessionHas('status', 'All CMS settings restored to defaults.');

        $this->assertSame(0, CmsSetting::count());
        $this->assertSame(config('cms.brand.site_name'), CmsContent::get('brand.site_name'));
        $this->assertSame(config('cms.palette.primary'), CmsContent::get('palette.primary'));
    }

    public function test_admin_cannot_reset_an_unknown_cms_key(): void
    {
        $admin = User::factory()->create(['accounttype' => 'admin']);

        CmsContent::set('brand', ['site_name' => 'Changed Care']);

        $this->withSession(['user' => $admin->toArray()])
            ->post('/admin/cms/reset', ['key' => 'not-a-real-default'])
            ->assertNotFound();

        $this->assertDatabaseHas('cms_settings', ['key' => 'brand']);
    }
}
