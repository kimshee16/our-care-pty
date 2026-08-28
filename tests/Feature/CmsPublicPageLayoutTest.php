<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsPublicPageLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_home_cms_pages_use_home_style_layout(): void
    {
        foreach (['/about', '/services', '/services/personal-care-support'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('cms-page-hero', false)
                ->assertSee('cms-page-footer', false)
                ->assertDontSee('cms-site-header', false);
        }
    }
}
