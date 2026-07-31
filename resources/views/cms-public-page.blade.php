@php
    if (! function_exists('cms_asset_url')) {
        function cms_asset_url(?string $path): string {
            if (!$path) {
                return asset('hero.jpg');
            }

            return str_starts_with($path, 'http://') || str_starts_with($path, 'https://') ? $path : asset($path);
        }
    }

    $heroImage = cms_asset_url($page['hero_image'] ?? 'hero.jpg');
    $logoUrl = cms_asset_url($brand['logo'] ?? 'logo3.png');
    $sections = $page['sections'] ?? [];
    $pageLinks = $pages ?? config('cms.pages');
    $pageUrls = [
        'home-v2' => 'cms/home',
        'about-v2' => 'cms/about',
        'services-v2' => 'cms/services',
        'onboarding-v2' => 'cms/onboarding',
        'intake-v2' => 'cms/intake',
        'contact-v2' => 'cms/contact',
    ];
    $introParagraphs = array_values(array_filter(array_map('trim', explode("\n\n", str_replace(["\r\n", "\r"], "\n", $page['intro_text'] ?? '')))));
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page['title'] ?? ($brand['site_name'] ?? 'Our Care') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
<style>
    :root {
        --oc-primary: {{ $palette['primary'] ?? '#674cbf' }};
        --oc-secondary: {{ $palette['secondary'] ?? '#6b46c1' }};
        --oc-accent: {{ $palette['accent'] ?? '#36b24a' }};
        --oc-background: {{ $palette['background'] ?? '#ffffff' }};
        --oc-surface: {{ $palette['surface'] ?? '#f8f9fa' }};
        --oc-text: {{ $palette['text'] ?? '#1f2937' }};
    }

    * { box-sizing: border-box; }
    body { margin: 0; font-family: Arial, Helvetica, sans-serif; color: var(--oc-text); background: var(--oc-background); }
    .cms-site-header { position: sticky; top: 0; z-index: 20; background: rgba(255,255,255,.96); border-bottom: 1px solid #e5e7eb; box-shadow: 0 2px 12px rgba(31, 41, 55, .08); }
    .cms-nav { width: min(1180px, calc(100% - 36px)); margin: 0 auto; min-height: 82px; display: flex; align-items: center; justify-content: space-between; gap: 24px; }
    .cms-brand { display: inline-flex; align-items: center; gap: 12px; color: var(--oc-primary); text-decoration: none; font-weight: 900; }
    .cms-brand img { width: 64px; height: 64px; object-fit: contain; }
    .cms-brand span { display: grid; line-height: 1.1; }
    .cms-brand small { color: #64748b; font-weight: 700; font-size: 12px; }
    .cms-menu { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; justify-content: flex-end; }
    .cms-menu a { color: #374151; text-decoration: none; font-weight: 800; padding: 10px 12px; border-radius: 8px; }
    .cms-menu a:hover, .cms-menu a.active { color: var(--oc-primary); background: color-mix(in srgb, var(--oc-primary) 10%, #fff); }
    .cms-menu a.cms-login { background: var(--oc-primary); color: #fff; }
    .cms-public-page { color: var(--oc-text); background: var(--oc-background); }
    .cms-hero { min-height: calc(100vh - 82px); display: grid; align-items: end; background: linear-gradient(90deg, rgba(18, 12, 42, .76), rgba(18, 12, 42, .18)), url('{{ $heroImage }}') center/cover; }
    .cms-hero-inner { width: min(1180px, calc(100% - 40px)); margin: 0 auto; padding: 110px 0 80px; color: #fff; }
    .cms-hero h1 { max-width: 780px; margin: 0; font-size: clamp(42px, 6vw, 78px); line-height: .98; letter-spacing: 0; }
    .cms-hero p { max-width: 700px; margin: 22px 0 0; font-size: 20px; line-height: 1.6; }
    .cms-band { padding: 76px 20px; background: var(--oc-background); }
    .cms-band.alt { background: var(--oc-surface); }
    .cms-inner { width: min(1180px, 100%); margin: 0 auto; }
    .cms-kicker { color: var(--oc-accent); font-weight: 800; text-transform: uppercase; font-size: 13px; letter-spacing: .08em; }
    .cms-copy { max-width: 850px; }
    .cms-copy h2 { margin: 10px 0 16px; font-size: clamp(30px, 4vw, 48px); color: var(--oc-primary); letter-spacing: 0; }
    .cms-copy p { font-size: 18px; line-height: 1.75; color: var(--oc-text); }
    .cms-registration { display: inline-block; margin-top: 18px; padding: 10px 14px; border-radius: 999px; background: color-mix(in srgb, var(--oc-primary) 12%, #fff); color: var(--oc-primary); font-weight: 800; }
    .cms-section-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; margin-top: 34px; }
    .cms-section-card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; background: #fff; box-shadow: 0 8px 24px rgba(31, 41, 55, .08); }
    .cms-section-card h3 { margin: 0 0 10px; color: var(--oc-primary); font-size: 21px; }
    .cms-section-card p { margin: 0; color: #4b5563; line-height: 1.65; }
    .cms-service-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; margin-top: 34px; }
    .cms-service-card { display: grid; min-height: 260px; overflow: hidden; border-radius: 8px; background: #fff; border: 1px solid #e5e7eb; text-decoration: none; color: inherit; box-shadow: 0 8px 24px rgba(31, 41, 55, .08); }
    .cms-service-card img { width: 100%; height: 150px; object-fit: cover; }
    .cms-service-card div { padding: 18px; }
    .cms-service-card h3 { margin: 0 0 8px; color: var(--oc-primary); font-size: 20px; }
    .cms-service-card p { margin: 0; color: #4b5563; line-height: 1.55; }
    .cms-cta { background: var(--oc-primary); color: #fff; padding: 58px 20px; }
    .cms-cta .cms-inner { display: flex; align-items: center; justify-content: space-between; gap: 24px; }
    .cms-cta h2 { margin: 0; font-size: 34px; }
    .cms-cta a { display: inline-flex; align-items: center; justify-content: center; min-height: 46px; padding: 0 18px; border-radius: 8px; background: var(--oc-accent); color: #fff; text-decoration: none; font-weight: 800; }
    .cms-footer { background: #111827; color: #d1d5db; padding: 30px 20px; }
    .cms-footer .cms-inner { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
    .cms-footer a { color: #fff; text-decoration: none; font-weight: 800; }
    @media (max-width: 900px) { .cms-nav { align-items: flex-start; flex-direction: column; padding: 14px 0; } .cms-menu { justify-content: flex-start; } .cms-section-grid, .cms-service-grid { grid-template-columns: 1fr; } .cms-cta .cms-inner { align-items: flex-start; flex-direction: column; } .cms-hero { min-height: 500px; } }
</style>
</head>
<body>
<header class="cms-site-header">
    <nav class="cms-nav" aria-label="Primary navigation">
        <a href="{{ url('/cms/home') }}" class="cms-brand">
            <img src="{{ $logoUrl }}" alt="{{ $brand['site_name'] ?? 'Our Care' }} logo">
            <span>
                {{ $brand['site_name'] ?? 'Our Care' }}
                <small>{{ $brand['tagline'] ?? '' }}</small>
            </span>
        </a>
        <div class="cms-menu">
            @foreach($pageLinks as $slug => $linkPage)
                <a href="{{ url('/' . ($pageUrls[$slug] ?? $slug)) }}" class="{{ ($pageSlug ?? '') === $slug ? 'active' : '' }}">{{ $linkPage['label'] ?? $slug }}</a>
            @endforeach
            <a href="{{ url('/login') }}" class="cms-login">Login</a>
        </div>
    </nav>
</header>

<main class="cms-public-page">
    <section class="cms-hero">
        <div class="cms-hero-inner">
            <div class="cms-kicker">{{ $brand['tagline'] ?? 'Our Care' }}</div>
            <h1>{{ $page['hero_title'] ?? $page['title'] ?? 'Our Care' }}</h1>
            @if(!empty($page['hero_subtitle']))
                <p>{{ $page['hero_subtitle'] }}</p>
            @endif
        </div>
    </section>

    <section class="cms-band">
        <div class="cms-inner cms-copy">
            <div class="cms-kicker">{{ $page['label'] ?? 'Our Care' }}</div>
            <h2>{{ $page['intro_title'] ?? $page['title'] ?? 'Our Care' }}</h2>
            @foreach($introParagraphs as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
            @if(!empty($page['registration']))
                <span class="cms-registration">{{ $page['registration'] }}</span>
            @endif
        </div>
    </section>

    @if(!empty($page['section_heading']) || !empty($page['section_intro']) || count($sections))
        <section class="cms-band alt">
            <div class="cms-inner">
                <div class="cms-copy">
                    @if(!empty($page['section_heading']))
                        <h2>{{ $page['section_heading'] }}</h2>
                    @endif
                    @if(!empty($page['section_intro']))
                        <p>{{ $page['section_intro'] }}</p>
                    @endif
                </div>
                @if(count($sections))
                    <div class="cms-section-grid">
                        @foreach($sections as $section)
                            <article class="cms-section-card">
                                <h3>{{ $section['title'] ?? '' }}</h3>
                                <p>{{ $section['text'] ?? '' }}</p>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    @if(($pageSlug ?? '') === 'services-v2' && empty($serviceSlug))
        <section class="cms-band alt">
            <div class="cms-inner">
                <div class="cms-service-grid">
                    @foreach($services as $slug => $service)
                        <a class="cms-service-card" href="{{ url('/cms/services/' . $slug) }}">
                            <img src="{{ cms_asset_url($service['image'] ?? 'hero.jpg') }}" alt="{{ $service['label'] ?? 'Service' }}">
                            <div>
                                <h3>{{ $service['label'] ?? $service['title'] ?? 'Service' }}</h3>
                                <p>{{ $service['summary'] ?? '' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="cms-cta">
        <div class="cms-inner">
            <h2>{{ $brand['site_name'] ?? 'Our Care Pty Ltd' }}</h2>
            <a href="{{ url('/cms/contact') }}">Contact Us</a>
        </div>
    </section>
</main>

<footer class="cms-footer">
    <div class="cms-inner">
        <span>&copy; {{ date('Y') }} {{ $brand['site_name'] ?? 'Our Care Pty Ltd' }}</span>
        <a href="{{ url('/cms/contact') }}">Contact</a>
    </div>
</footer>
</body>
</html>
