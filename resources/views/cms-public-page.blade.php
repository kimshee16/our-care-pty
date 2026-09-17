@extends('layouts.public-v2', [
    'activePage' => match ($pageSlug ?? '') {
        'about-v2' => 'about',
        'services-v2' => 'services',
        'onboarding-v2' => 'onboarding',
        'intake-v2' => 'intake',
        'contact-v2' => 'contact',
        default => 'services',
    },
])

@section('title', ($page['title'] ?? ($brand['site_name'] ?? 'Our Care')) . ' - ' . ($brand['site_name'] ?? 'Our Care'))

@section('styles')
    @php
        $palette = array_replace(config('cms.palette', []), $palette ?? \App\Support\CmsContent::get('palette', []));
        $fontFamilies = config('cms.font_families', []);
        $typography = $page['typography'] ?? [];
    @endphp
    :root {
        --home-plum: {{ $palette['primary'] ?? '#2d124b' }};
        --home-secondary: {{ $palette['secondary'] ?? '#e6badf' }};
        --home-orange: {{ $palette['accent'] ?? '#ff7044' }};
        --home-bg: {{ $palette['background'] ?? '#fffaf7' }};
        --home-surface: {{ $palette['surface'] ?? '#fff8f2' }};
        --home-ink: {{ $palette['text'] ?? '#2c1746' }};
        --home-muted: color-mix(in srgb, var(--home-ink) 68%, #fff);
        --home-hero-start: color-mix(in srgb, var(--home-secondary) 76%, #fff);
        --home-hero-mid: color-mix(in srgb, var(--home-secondary) 56%, var(--home-orange));
        --home-hero-end: color-mix(in srgb, var(--home-orange) 30%, #fff);
        --home-content: 1180px;
        --home-hero-content: 1544px;
    }

    @foreach($typography as $sectionKey => $sectionTypography)
        @php
            $sectionClass = preg_replace('/[^a-z0-9_-]/i', '', (string) $sectionKey);
            $familyKey = $sectionTypography['font_family'] ?? '';
            $familyStack = $familyKey && $familyKey !== 'default' ? ($fontFamilies[$familyKey] ?? null) : null;
            $fontSize = isset($sectionTypography['font_size']) ? (int) $sectionTypography['font_size'] : null;
            $fontColor = $sectionTypography['font_color'] ?? null;
        @endphp
        @if($sectionClass && ($familyStack || $fontSize || $fontColor))
            .cms-typo-{{ $sectionClass }} :where(h1,h2,h3,p,small,a,span,strong,li) {
                @if($familyStack) font-family: {!! $familyStack !!} !important; @endif
                @if($fontSize) font-size: {{ $fontSize }}px !important; @endif
                @if($fontColor) color: {{ $fontColor }} !important; @endif
            }
        @endif
    @endforeach

    body { background: var(--home-bg); }
    .topbar { min-height: 47px; padding: 8px max(18px, calc((100vw - var(--home-hero-content)) / 2)); border-bottom: 0; color: rgba(255,255,255,.9); background: var(--home-plum); font-size: 14px; line-height: 1.15; }
    .topbar a { color: #fff; font-size: 14px; font-weight: 800; }
    .topbar .icon { width: 21px; height: 21px; }
    .topbar__group { gap: 26px; }
    .topbar__item { gap: 9px; }
    .site-header { min-height: 111px; padding: 14px max(18px, calc((100vw - var(--home-hero-content)) / 2)); border-bottom: 1px solid rgba(45,18,75,.08); background: rgba(255,255,255,.98); box-shadow: none; }
    .brand-link { min-width: 260px; }
    .brand-link img { width: 250px; height: 92px; object-fit: contain; object-position: left center; }
    .nav-links { gap: 27px; color: var(--home-ink); font-size: 18px; font-weight: 700; }
    .nav-links a, .nav-trigger { padding: 12px 0; }
    .nav-links a:hover, .nav-links a[aria-current="page"], .nav-trigger:hover, .nav-item:hover .nav-trigger { color: var(--home-orange); }
    .footer { display: none; }

    .cms-page-btn { display: inline-flex; align-items: center; justify-content: center; min-height: 40px; padding: 11px 18px; border: 0; border-radius: 999px; color: #fff; background: var(--home-orange); box-shadow: 0 12px 24px rgba(255,112,68,.26); font-size: 12px; font-weight: 900; line-height: 1; text-decoration: none; }
    .cms-page-btn:hover { transform: translateY(-1px); box-shadow: 0 16px 30px rgba(255,112,68,.32); }
    .cms-page-wrap { width: min(100%, 1080px); margin: 0 auto; }
    .cms-page-section { padding: clamp(54px, 7vw, 92px) var(--pad); background: var(--home-bg); }
    .cms-page-section.flush { padding-top: 0; }
    .cms-page-section.soft { background: var(--home-surface); }
    .cms-page-section.warm { background: linear-gradient(180deg, color-mix(in srgb, var(--home-orange) 20%, #fff) 0%, color-mix(in srgb, var(--home-surface) 82%, #fff4cf) 100%); }
    .cms-page-heading { max-width: 760px; margin: 0 auto 34px; text-align: center; }
    .cms-page-heading small { display: block; margin-bottom: 10px; color: var(--home-orange); font-size: 12px; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; }
    .cms-page-heading h2 { margin: 0 0 12px; color: var(--home-ink); font-size: clamp(1.7rem, 3.2vw, 2.35rem); line-height: 1.15; }
    .cms-page-heading p { margin: 0; color: var(--home-muted); font-size: 14px; line-height: 1.8; }

    .cms-page-hero { overflow: hidden; min-height: min(620px, calc(100vh - 80px)); padding: clamp(58px, 8vw, 104px) var(--pad) 0; background: linear-gradient(100deg, var(--home-hero-start) 0%, var(--home-hero-mid) 50%, var(--home-hero-end) 100%); }
    .cms-page-hero-grid { display: grid; grid-template-columns: minmax(0,.88fr) minmax(330px,1.12fr); gap: clamp(24px, 6vw, 76px); align-items: end; width: min(100%, 1120px); min-height: 510px; margin: 0 auto; }
    .cms-page-hero-copy { align-self: center; padding-bottom: 72px; }
    .cms-page-hero h1 { max-width: 570px; margin: 0 0 18px; color: var(--home-ink); font-size: clamp(2.35rem, 5.5vw, 4.6rem); line-height: 1.03; }
    .cms-page-hero p { max-width: 520px; margin: 0 0 24px; color: #443454; font-size: clamp(.98rem,1.6vw,1.16rem); line-height: 1.7; }
    .cms-page-hero-note { display: block; margin-bottom: 14px; color: var(--home-orange); font-size: 12px; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; }
    .cms-page-hero-media { align-self: stretch; display: flex; align-items: flex-end; min-height: 460px; }
    .cms-page-hero-media img { width: 100%; height: min(560px,100%); object-fit: cover; object-position: center top; border-radius: 8px 8px 0 0; box-shadow: 0 24px 60px rgba(45,18,75,.18); }

    .cms-page-intro { display: grid; grid-template-columns: minmax(0,1.05fr) minmax(280px,.95fr); gap: clamp(28px,5vw,70px); align-items: start; }
    .cms-page-intro.solo { display: block; max-width: 760px; margin: 0 auto; text-align: center; }
    .cms-page-copy p { margin: 0 0 18px; color: var(--home-muted); font-size: 14px; line-height: 1.85; }
    .cms-page-registration { display: inline-flex; margin-top: 4px; padding: 10px 14px; border-radius: 999px; color: var(--home-ink); background: var(--home-surface); box-shadow: 0 10px 22px rgba(45,18,75,.06); font-size: 12px; font-weight: 900; }
    .cms-page-section-grid, .cms-page-service-grid { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 24px; }
    .cms-page-inline-grid { margin-top: 52px; }
    .cms-page-card { min-height: 170px; padding: 26px; border-radius: 8px; background: #fff; box-shadow: 0 14px 30px rgba(45,18,75,.08); }
    .cms-page-card h3 { margin: 0 0 10px; color: var(--home-ink); font-size: 20px; line-height: 1.2; }
    .cms-page-card p { margin: 0; color: var(--home-muted); font-size: 13px; line-height: 1.65; }
    .cms-page-service-grid { grid-template-columns: repeat(4,minmax(0,1fr)); gap: 26px 28px; }
    .cms-page-service-tile { display: grid; gap: 10px; color: var(--home-ink); font-weight: 900; text-align: center; text-decoration: none; }
    .cms-page-service-image { overflow: hidden; aspect-ratio: 1.18/1; border-radius: 8px; background: #f4edf6; box-shadow: 0 14px 28px rgba(45,18,75,.12); }
    .cms-page-service-image img { width: 100%; height: 100%; object-fit: cover; transition: transform .25s ease; }
    .cms-page-service-tile:hover img { transform: scale(1.04); }
    .cms-page-service-tile strong { min-height: 36px; color: var(--home-ink); font-size: 13px; line-height: 1.3; }
    .cms-page-service-tile span:last-child { color: var(--home-muted); font-size: 12px; font-weight: 600; line-height: 1.45; }

    .cms-page-cta { position: relative; overflow: hidden; min-height: 300px; padding: 84px var(--pad); color: #fff; background: var(--home-plum); text-align: center; }
    .cms-page-cta img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: .36; }
    .cms-page-cta:after { content: ""; position: absolute; inset: 0; background: rgba(31,14,49,.54); }
    .cms-page-cta-content { position: relative; z-index: 1; max-width: 760px; margin: 0 auto; }
    .cms-page-cta h2 { margin: 0 0 12px; color: #fff; font-size: clamp(1.9rem,4vw,3rem); }
    .cms-page-cta p { margin: 0 auto 24px; color: rgba(255,255,255,.88); font-size: 14px; }
    .cms-page-footer { padding: 48px var(--pad) 28px; color: rgba(255,255,255,.78); background: var(--home-plum); }
    .cms-page-footer-grid { display: grid; grid-template-columns: minmax(220px,1.2fr) repeat(3,minmax(140px,1fr)); gap: 36px; width: min(100%,var(--home-content)); margin: 0 auto 32px; }
    .cms-page-footer img { width: 170px; height: auto; padding: 0; border-radius: 0; background: transparent; object-fit: contain; }
    .cms-page-footer h3 { margin: 0 0 32px; color: #fff; font-size: 28px; font-weight: 900; line-height: 1.15; }
    .cms-page-footer a, .cms-page-footer p { display: block; margin: 0 0 24px; color: rgba(255,255,255,.92); font-size: 18px; font-weight: 700; line-height: 1.55; text-decoration: none; }
    .cms-page-footer-bottom { display: flex; justify-content: space-between; gap: 18px; width: min(100%,var(--home-content)); margin: 0 auto; padding-top: 28px; border-top: 1px solid rgba(255,255,255,.15); color: rgba(255,255,255,.92); font-size: 18px; font-weight: 700; }
    .cms-page-chat { position: fixed; right: 22px; bottom: 22px; z-index: 25; display: grid; place-items: center; width: 46px; height: 46px; border-radius: 50%; color: #fff; background: var(--home-orange); box-shadow: 0 16px 32px rgba(255,112,68,.34); font-size: 22px; font-weight: 900; text-decoration: none; }

    @media (max-width: 980px) {
        body .site-header { align-items: center; flex-direction: row; flex-wrap: wrap; gap: 8px 18px; }
        body .brand-link { justify-content: flex-start; min-width: auto; }
        body .brand-link img { width: 220px; height: 82px; }
        body .nav-links { flex: 1 1 100%; justify-content: center; gap: 14px; max-width: 100%; overflow: hidden; }
        .submenu { display: none !important; }
        .cms-page-hero-grid, .cms-page-intro, .cms-page-footer-grid { grid-template-columns: 1fr; }
        .cms-page-hero { padding-bottom: 42px; }
        .cms-page-hero-copy { padding-bottom: 0; }
        .cms-page-hero-media { min-height: 360px; }
        .cms-page-section-grid, .cms-page-service-grid { grid-template-columns: repeat(2,minmax(0,1fr)); }
    }

    @media (max-width: 640px) {
        body .topbar { align-items: center; gap: 8px; min-width: 0; padding-left: 16px; padding-right: 16px; }
        body .topbar__group { justify-content: center; width: 100%; gap: 8px 12px; }
        body .topbar__item { white-space: normal; }
        body .topbar__group:first-child .topbar__item:nth-child(2) { display: none; }
        body .site-header { justify-content: center; min-width: 0; padding-left: 16px; padding-right: 16px; }
        body .brand-link img { width: 190px; height: 70px; }
        body .nav-links { justify-content: flex-start; gap: 12px 14px; width: 100%; min-width: 0; overflow-x: auto; overflow-y: hidden; padding-bottom: 5px; font-size: 9px; line-height: 1.2; scrollbar-width: none; }
        body .nav-links::-webkit-scrollbar { display: none; }
        body .nav-links a, body .nav-trigger { white-space: nowrap; }
        .cms-page-hero { min-height: auto; padding-top: 46px; }
        .cms-page-hero-grid, .cms-page-section-grid, .cms-page-service-grid { grid-template-columns: 1fr; }
        .cms-page-hero-grid { min-height: auto; }
        .cms-page-hero-media { min-height: 290px; }
        .cms-page-footer-bottom { flex-direction: column; }
    }
@endsection

@section('content')
    @php
        $brand = $brand ?? \App\Support\CmsContent::get('brand', config('cms.brand'));
        $pages = $pages ?? \App\Support\CmsContent::get('pages', config('cms.pages', []));
        $services = $services ?? \App\Support\CmsContent::services();
        $assetUrl = function (?string $path): string {
            $path = trim((string) $path);
            if ($path === '') return asset('hero.jpg');
            return str_starts_with($path, 'http://') || str_starts_with($path, 'https://') ? $path : asset($path);
        };
        $pageUrl = function (?string $path): string {
            $path = trim((string) $path);
            if ($path === '') return '#';
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '#')) return $path;
            return url($path);
        };
        $phoneHref = fn (?string $phone): string => 'tel:' . preg_replace('/\D+/', '', (string) $phone);
        $introParagraphs = array_values(array_filter(array_map('trim', explode("\n\n", str_replace(["\r\n", "\r"], "\n", $page['intro_text'] ?? '')))));
        $sections = $page['sections'] ?? [];
        $renderSectionsWithIntro = count($sections) && empty($page['section_heading']) && empty($page['section_intro']);
        $serviceFooterLinks = array_slice($services, 0, 3, true);
        $homePage = \App\Support\CmsContent::page('home-v2');
        $locations = $homePage['locations'] ?? [];
    @endphp

    <section class="cms-page-hero cms-typo-hero">
        <div class="cms-page-hero-grid">
            <div class="cms-page-hero-copy">
                <span class="cms-page-hero-note">{{ $brand['tagline'] ?? 'Our Care' }}</span>
                <h1>{{ $page['hero_title'] ?? $page['title'] ?? 'Our Care' }}</h1>
                @if(!empty($page['hero_subtitle']))
                    <p>{{ $page['hero_subtitle'] }}</p>
                @endif
            </div>
            <div class="cms-page-hero-media">
                <img src="{{ $assetUrl($page['hero_image'] ?? 'hero.jpg') }}" alt="{{ $page['title'] ?? 'Our Care' }}">
            </div>
        </div>
    </section>

    <section class="cms-page-section">
        <div class="cms-page-wrap">
            <div class="cms-page-heading cms-typo-intro">
                <small>{{ $page['label'] ?? 'Our Care' }}</small>
                <h2>{{ $page['intro_title'] ?? $page['title'] ?? 'Our Care' }}</h2>
                @if(empty($introParagraphs) && !empty($page['summary']))
                    <p>{{ $page['summary'] }}</p>
                @endif
            </div>
            <div class="cms-page-intro cms-typo-intro @if(empty($page['summary'])) solo @endif">
                <div class="cms-page-copy">
                    @foreach($introParagraphs as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                    @if(!empty($page['registration']))
                        <span class="cms-page-registration">{{ $page['registration'] }}</span>
                    @endif
                </div>
                @if(!empty($page['summary']))
                    <article class="cms-page-card">
                        <h3>{{ $page['heading'] ?? $page['title'] ?? 'Our Care' }}</h3>
                        <p>{{ $page['summary'] }}</p>
                    </article>
                @endif
            </div>
            @if($renderSectionsWithIntro)
                <div class="cms-page-section-grid cms-page-inline-grid cms-typo-sections">
                    @foreach($sections as $section)
                        <article class="cms-page-card">
                            <h3>{{ $section['title'] ?? '' }}</h3>
                            <p>{{ $section['text'] ?? '' }}</p>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @if(! $renderSectionsWithIntro && (!empty($page['section_heading']) || !empty($page['section_intro']) || count($sections)))
        <section class="cms-page-section soft cms-typo-sections @if(empty($page['section_heading']) && empty($page['section_intro'])) flush @endif">
            <div class="cms-page-wrap">
                @if(!empty($page['section_heading']) || !empty($page['section_intro']))
                    <div class="cms-page-heading">
                    @if(!empty($page['section_heading']))
                        <h2>{{ $page['section_heading'] }}</h2>
                    @endif
                    @if(!empty($page['section_intro']))
                        <p>{{ $page['section_intro'] }}</p>
                    @endif
                    </div>
                @endif
                @if(count($sections))
                    <div class="cms-page-section-grid">
                        @foreach($sections as $section)
                            <article class="cms-page-card">
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
        <section class="cms-page-section warm cms-typo-service_grid">
            <div class="cms-page-wrap">
                <div class="cms-page-heading">
                    <h2>{{ $page['service_grid_heading'] ?? config('cms.pages.services-v2.service_grid_heading') }}</h2>
                </div>
                <div class="cms-page-service-grid">
                    @foreach($services as $slug => $service)
                        <a class="cms-page-service-tile" href="{{ route('services.detail.v2', $slug) }}">
                            <span class="cms-page-service-image"><img src="{{ $assetUrl($service['image'] ?? 'hero.jpg') }}" alt="{{ $service['label'] ?? 'Service' }}"></span>
                            <strong>{{ $service['label'] ?? $service['title'] ?? 'Service' }}</strong>
                            <span>{{ $service['summary'] ?? '' }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="cms-page-cta cms-typo-cta">
        <img src="{{ $assetUrl($homePage['cta_image'] ?? config('cms.pages.home-v2.cta_image')) }}" alt="{{ $homePage['cta_title'] ?? config('cms.pages.home-v2.cta_title') }}">
        <div class="cms-page-cta-content">
            <h2>{{ $homePage['cta_title'] ?? 'Get started today' }}</h2>
            <p>{{ $homePage['cta_text'] ?? 'Our professional and helpful team is ready to guide the next step for your support needs.' }}</p>
            <a class="cms-page-btn" href="{{ $pageUrl($homePage['cta_url'] ?? '/intake-v2') }}">{{ $homePage['cta_button_label'] ?? 'Book a free consultation' }}</a>
        </div>
    </section>

    <section class="cms-page-footer cms-typo-footer">
        <div class="cms-page-footer-grid">
            <div>
                <img src="{{ $assetUrl($brand['logo'] ?? 'logo3.png') }}" alt="{{ $brand['site_name'] ?? 'Our Care' }} logo">
                <p>{{ $homePage['footer_text'] ?? config('cms.pages.home-v2.footer_text') }}</p>
            </div>
            <div>
                <h3>{{ $brand['footer_services_label'] ?? 'Services' }}</h3>
                <a href="{{ url('/services-v2') }}">{{ $pages['services-v2']['label'] ?? 'Services' }}</a>
                @foreach($serviceFooterLinks as $slug => $service)
                    <a href="{{ route('services.detail.v2', $slug) }}">{{ $service['label'] ?? $service['title'] ?? $slug }}</a>
                @endforeach
            </div>
            <div>
                <h3>{{ $brand['footer_quick_links_label'] ?? 'Quick Links' }}</h3>
                <a href="{{ url('/about-v2') }}">{{ ($pages['about-v2']['label'] ?? null) ?: 'About Us' }}</a>
                <a href="{{ url('/intake-v2') }}">{{ ($pages['intake-v2']['label'] ?? null) ?: 'Intake' }}</a>
                <a href="{{ url('/onboarding-v2') }}">{{ ($pages['onboarding-v2']['label'] ?? null) ?: 'Onboarding' }}</a>
                <a href="{{ url('/contact-v2') }}">{{ ($pages['contact-v2']['label'] ?? null) ?: 'Contact Us' }}</a>
            </div>
            <div>
                <h3>{{ $brand['footer_contact_label'] ?? 'Contact' }}</h3>
                @if(!empty($brand['phone']))
                    <a href="{{ $phoneHref($brand['phone']) }}">{{ $brand['phone'] }}</a>
                @endif
                @if(!empty($brand['email']))
                    <a href="mailto:{{ $brand['email'] }}">{{ $brand['email'] }}</a>
                @endif
                <p>{{ implode(', ', $locations) }}</p>
            </div>
        </div>
        <div class="cms-page-footer-bottom">
            <span>Copyright &copy; {{ date('Y') }} {{ $brand['site_name'] ?? 'Our Care Pty Ltd' }}.</span>
            <span>{{ $homePage['footer_credit'] ?? '' }}</span>
        </div>
    </section>

    <a class="cms-page-chat" href="{{ $pageUrl($brand['chat_url'] ?? config('cms.brand.chat_url')) }}" aria-label="{{ $brand['chat_aria_label'] ?? config('cms.brand.chat_aria_label') }}">{{ $brand['chat_label'] ?? config('cms.brand.chat_label') }}</a>
@endsection
