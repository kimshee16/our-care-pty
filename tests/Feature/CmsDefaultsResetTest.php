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
            ->assertSee('hero_background_start', false)
            ->assertSee('hero_slide_images[]', false)
            ->assertSee('cms-reset-service-personal-care-support', false);
    }

    public function test_cms_list_fields_replace_default_lists(): void
    {
        CmsContent::set('pages.home-v2', [
            'hero_slides' => [
                ['image' => 'cms/custom-slide.png', 'alt' => 'Custom slide'],
            ],
            'trust_items' => [],
        ]);

        $page = CmsContent::page('home-v2');

        $this->assertSame(
            [['image' => 'cms/custom-slide.png', 'alt' => 'Custom slide']],
            $page['hero_slides']
        );
        $this->assertSame([], $page['trust_items']);
        $this->assertSame(config('cms.pages.home-v2.hero_title'), $page['hero_title']);
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

    public function test_admin_can_configure_header_and_footer_logos_separately(): void
    {
        $admin = User::factory()->create(['accounttype' => 'admin']);

        $this->withSession(['user' => $admin->toArray()])
            ->post('/admin/cms/brand', [
                'site_name' => 'Our Care Pty Ltd',
                'logo_path' => 'cms/header-logo.png',
                'footer_logo_path' => 'cms/footer-logo.png',
            ])
            ->assertRedirect('/admin/cms');

        $this->assertSame('cms/header-logo.png', CmsContent::get('brand.logo'));
        $this->assertSame('cms/footer-logo.png', CmsContent::get('brand.footer_logo'));

        $this->get('/cms/home')
            ->assertOk()
            ->assertSee('cms/header-logo.png', false)
            ->assertSee('cms/footer-logo.png', false);
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

    public function test_admin_can_configure_home_hero_background_colors(): void
    {
        $admin = User::factory()->create(['accounttype' => 'admin']);

        $this->withSession(['user' => $admin->toArray()])
            ->post('/admin/cms/pages/home-v2', [
                'label' => 'Home',
                'title' => 'Our Care Pty Ltd',
                'hero_title' => 'Configured hero',
                'hero_background_start' => '#112233',
                'hero_background_mid' => '#445566',
                'hero_background_end' => '#778899',
            ])
            ->assertRedirect('/admin/cms#page-home-v2');

        $page = CmsContent::page('home-v2');

        $this->assertSame('#112233', $page['hero_background_start']);
        $this->assertSame('#445566', $page['hero_background_mid']);
        $this->assertSame('#778899', $page['hero_background_end']);

        $this->get('/cms/home')
            ->assertOk()
            ->assertSee('--home-hero-background: linear-gradient(100deg, #112233 0%, #445566 47%, #778899 100%);', false);
    }
}
