@extends('layouts.public-v2', ['activePage' => 'home'])

@section('title', ($page['title'] ?? 'Our Care') . ' - Home')

@section('styles')
    @php
        $palette = array_replace(config('cms.palette', []), $palette ?? \App\Support\CmsContent::get('palette', []));
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
    }

    body { background: var(--home-bg); }
    .topbar { min-height: 28px; padding-top: 5px; padding-bottom: 5px; border-bottom: 0; color: rgba(255,255,255,.86); background: var(--home-plum); font-size: 11px; }
    .topbar a { color: #fff; font-weight: 800; }
    .topbar .icon { width: 14px; height: 14px; }
    .site-header { min-height: 52px; padding-top: 8px; padding-bottom: 8px; border-bottom: 1px solid rgba(45,18,75,.08); background: rgba(255,255,255,.96); box-shadow: 0 10px 28px rgba(45,18,75,.06); }
    .brand-link img { width: 128px; height: 52px; object-fit: contain; }
    .brand-wordmark { display: none; }
    .nav-links { gap: 18px; color: var(--home-ink); font-size: 12px; }
    .nav-links a:hover, .nav-links a[aria-current="page"], .nav-trigger:hover, .nav-item:hover .nav-trigger { color: var(--home-orange); }
    .footer { display: none; }

    .button, .home-btn { display: inline-flex; align-items: center; justify-content: center; min-height: 40px; padding: 11px 18px; border: 0; border-radius: 999px; color: #fff; background: var(--home-orange); box-shadow: 0 12px 24px rgba(255,112,68,.26); font-size: 12px; font-weight: 900; line-height: 1; }
    .button:hover, .home-btn:hover { transform: translateY(-1px); box-shadow: 0 16px 30px rgba(255,112,68,.32); }

    .home-wrap { width: min(100%, 1080px); margin: 0 auto; }
    .home-section { padding: clamp(54px, 7vw, 92px) var(--pad); background: var(--home-bg); }
    .home-section.pink { background: linear-gradient(180deg, color-mix(in srgb, var(--home-secondary) 62%, #fff) 0%, color-mix(in srgb, var(--home-orange) 28%, #fff) 100%); }
    .home-section.warm { background: linear-gradient(180deg, color-mix(in srgb, var(--home-orange) 26%, #fff) 0%, color-mix(in srgb, var(--home-surface) 78%, #fff4cf) 100%); }
    .home-section.soft { background: var(--home-surface); }
    .home-heading { max-width: 760px; margin: 0 auto 34px; text-align: center; }
    .home-heading h2 { margin: 0 0 12px; color: var(--home-ink); font-size: clamp(1.7rem, 3.2vw, 2.35rem); line-height: 1.15; }
    .home-heading p { margin: 0; color: var(--home-muted); font-size: 14px; }

    .home-hero { overflow: hidden; min-height: min(620px, calc(100vh - 80px)); padding: clamp(58px, 8vw, 104px) var(--pad) 0; background: linear-gradient(100deg, var(--home-hero-start) 0%, var(--home-hero-mid) 50%, var(--home-hero-end) 100%); }
    .home-hero-grid { display: grid; grid-template-columns: minmax(0,.88fr) minmax(330px,1.12fr); gap: clamp(24px, 6vw, 76px); align-items: end; width: min(100%, 1120px); min-height: 510px; margin: 0 auto; }
    .home-hero-copy { align-self: center; padding-bottom: 72px; }
    .home-hero h1 { max-width: 570px; margin: 0 0 18px; color: var(--home-ink); font-size: clamp(2.35rem, 5.5vw, 4.6rem); line-height: 1.03; }
    .home-hero p { max-width: 520px; margin: 0 0 24px; color: #443454; font-size: clamp(.98rem,1.6vw,1.16rem); line-height: 1.7; }
    .home-note { display: block; max-width: 520px; margin-top: 16px; color: rgba(45,18,75,.66); font-size: 11px; line-height: 1.6; }
    .home-hero-media { align-self: stretch; display: flex; align-items: flex-end; min-height: 460px; }
    .home-hero-media img { width: 100%; height: min(560px,100%); object-fit: cover; object-position: center top; border-radius: 8px 8px 0 0; box-shadow: 0 24px 60px rgba(45,18,75,.18); }

    .intro-grid, .office-grid { display: grid; grid-template-columns: minmax(0,1.05fr) minmax(280px,.95fr); gap: clamp(28px,5vw,70px); }
    .intro-copy p { margin-bottom: 18px; color: var(--home-muted); font-size: 14px; line-height: 1.85; }
    .fact-list { display: grid; gap: 16px; padding: 0; margin: 0; list-style: none; }
    .fact-list li { padding: 15px 16px; border-radius: 8px; color: var(--home-ink); background: var(--home-surface); box-shadow: 0 10px 22px rgba(45,18,75,.06); font-size: 14px; font-weight: 800; }

    .pathway-grid, .event-grid, .testimonial-grid, .update-grid, .requirement-grid { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 24px; }
    .pathway-grid { margin-top: 36px; }
    .image-card { position: relative; overflow: hidden; min-height: 170px; padding: 22px; border-radius: 8px; color: #fff; background: var(--home-plum); box-shadow: 0 16px 32px rgba(45,18,75,.16); }
    .image-card img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: .56; }
    .image-card:after { content: ""; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(45,18,75,.12), rgba(45,18,75,.78)); }
    .image-card-body { position: relative; z-index: 1; display: grid; align-content: end; min-height: 126px; }
    .image-card h3 { margin: 0 0 8px; font-size: 20px; line-height: 1.1; }
    .image-card p { margin: 0; color: rgba(255,255,255,.88); font-size: 12px; line-height: 1.45; }

    .trust-grid, .service-grid { display: grid; grid-template-columns: repeat(4,minmax(0,1fr)); gap: 26px 28px; }
    .trust-grid { grid-template-columns: repeat(3,minmax(0,1fr)); margin-bottom: 34px; }
    .trust-card { display: grid; justify-items: center; text-align: center; }
    .trust-icon { display: grid; place-items: center; width: 42px; height: 42px; border-radius: 50%; color: #fff; background: var(--home-plum); font-weight: 900; }
    .trust-card h3 { margin: 12px 0 4px; color: var(--home-ink); font-size: 15px; }
    .trust-card p { margin: 0; color: #6f5575; font-size: 12px; line-height: 1.55; }
    .rating-card, .testimonial-card, .update-card, .requirement-card { border-radius: 8px; background: #fff; box-shadow: 0 14px 30px rgba(45,18,75,.08); }
    .rating-card { width: min(100%,430px); margin: 0 auto 28px; padding: 22px 26px; text-align: center; }
    .stars { margin-bottom: 10px; color: #ffc107; font-size: 20px; letter-spacing: 0; }
    .rating-card p, .testimonial-card p { margin: 0; color: var(--home-muted); font-size: 12px; line-height: 1.65; }

    .service-tile { display: grid; gap: 10px; color: var(--home-ink); font-weight: 900; text-align: center; }
    .service-tile-image { overflow: hidden; aspect-ratio: 1.18/1; border-radius: 8px; background: #f4edf6; box-shadow: 0 14px 28px rgba(45,18,75,.12); }
    .service-tile-image img { width: 100%; height: 100%; object-fit: cover; transition: transform .25s ease; }
    .service-tile:hover img { transform: scale(1.04); }
    .service-tile span:last-child { min-height: 36px; font-size: 13px; line-height: 1.3; }

    .event-card { display: grid; justify-items: center; gap: 14px; text-align: center; }
    .event-poster { position: relative; overflow: hidden; width: 100%; aspect-ratio: 4/3; border-radius: 8px; background: var(--home-plum); box-shadow: 0 18px 34px rgba(45,18,75,.16); }
    .event-poster img { width: 100%; height: 100%; object-fit: cover; opacity: .28; }
    .event-poster-copy { position: absolute; inset: 18px; display: grid; align-content: center; justify-items: center; color: #fff; text-align: center; }
    .event-poster-copy small { margin-bottom: 10px; padding: 5px 9px; border-radius: 999px; color: var(--home-plum); background: #fff; font-weight: 900; }
    .event-poster-copy strong { display: block; max-width: 250px; font-size: clamp(1.05rem,2vw,1.65rem); line-height: 1.08; text-transform: uppercase; }
    .event-card h3 { margin: 0; color: var(--home-ink); font-size: 15px; line-height: 1.35; }
    .event-card p { margin: -6px 0 0; color: var(--home-muted); font-size: 11px; font-weight: 800; text-transform: uppercase; }
    .testimonial-card { padding: 28px 24px; min-height: 180px; text-align: center; }
    .testimonial-card strong { color: var(--home-ink); font-size: 12px; }

    .requirement-card { padding: 28px; }
    .requirement-card:nth-child(1) { background: #d6a9d9; }
    .requirement-card:nth-child(2) { background: #ffc1d7; }
    .requirement-card:nth-child(3) { background: #ffd8ad; }
    .requirement-card h3 { margin: 0 0 8px; color: var(--home-ink); font-size: 22px; line-height: 1.05; }
    .requirement-card p { margin: 0 0 18px; color: #443454; font-size: 12px; line-height: 1.55; }
    .check-list { display: grid; gap: 9px; margin: 0 0 22px; padding: 0; list-style: none; }
    .check-list li { position: relative; padding-left: 24px; color: #332243; font-size: 12px; line-height: 1.45; }
    .check-list li:before { content: ""; position: absolute; left: 0; top: 2px; width: 15px; height: 15px; border-radius: 50%; background: var(--home-orange); }

    .update-card { overflow: hidden; }
    .update-card img { width: 100%; aspect-ratio: 1.45/1; object-fit: cover; }
    .update-card-body { padding: 18px; }
    .update-card h3 { margin: 0 0 8px; color: var(--home-ink); font-size: 15px; line-height: 1.35; }
    .update-card p { margin: 0 0 12px; color: var(--home-muted); font-size: 12px; line-height: 1.65; }
    .text-link { color: var(--home-orange); font-size: 12px; font-weight: 900; }

    .cta-band { position: relative; overflow: hidden; min-height: 360px; padding: 84px var(--pad); color: #fff; background: var(--home-plum); text-align: center; }
    .cta-band img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: .48; }
    .cta-band:after { content: ""; position: absolute; inset: 0; background: rgba(31,14,49,.48); }
    .cta-content { position: relative; z-index: 1; max-width: 760px; margin: 0 auto; }
    .cta-band h2 { margin: 0 0 12px; color: #fff; font-size: clamp(1.9rem,4vw,3rem); }
    .cta-band p { margin: 0 auto 24px; color: rgba(255,255,255,.88); font-size: 14px; }
    .office-grid { width: min(100%,760px); margin: 32px auto 0; grid-template-columns: repeat(2,minmax(0,1fr)); }
    .office-card h3 { margin: 0 0 12px; color: var(--home-ink); font-size: 16px; }
    .office-card p { margin: 0 0 8px; color: var(--home-muted); font-size: 12px; line-height: 1.5; }
    .location-pills { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; margin-top: 18px; }
    .location-pills span { padding: 8px 12px; border-radius: 999px; color: #fff; background: var(--home-orange); font-size: 11px; font-weight: 900; }

    .home-footer { padding: 52px var(--pad) 34px; color: rgba(255,255,255,.78); background: var(--home-plum); }
    .home-footer-grid { display: grid; grid-template-columns: minmax(220px,1.2fr) repeat(3,minmax(140px,1fr)); gap: 36px; width: min(100%,1080px); margin: 0 auto 34px; }
    .home-footer img { width: 170px; height: auto; padding: 8px 10px; border-radius: 8px; background: #fff; }
    .home-footer h3 { margin: 0 0 14px; color: #fff; font-size: 15px; }
    .home-footer a, .home-footer p { display: block; margin: 0 0 9px; color: rgba(255,255,255,.78); font-size: 12px; line-height: 1.55; }
    .home-footer-bottom { display: flex; justify-content: space-between; gap: 18px; width: min(100%,1080px); margin: 0 auto; padding-top: 22px; border-top: 1px solid rgba(255,255,255,.15); font-size: 11px; }
    .chat-button { position: fixed; right: 22px; bottom: 22px; z-index: 25; display: grid; place-items: center; width: 46px; height: 46px; border-radius: 50%; color: #fff; background: var(--home-orange); box-shadow: 0 16px 32px rgba(255,112,68,.34); font-size: 22px; font-weight: 900; }

    @media (max-width: 980px) {
        .home-hero-grid, .intro-grid, .home-footer-grid { grid-template-columns: 1fr; }
        .home-hero { padding-bottom: 42px; }
        .home-hero-copy { padding-bottom: 0; }
        .home-hero-media { min-height: 360px; }
        .pathway-grid, .trust-grid, .service-grid, .event-grid, .testimonial-grid, .requirement-grid, .update-grid { grid-template-columns: repeat(2,minmax(0,1fr)); }
    }

    @media (max-width: 640px) {
        .home-hero { min-height: auto; padding-top: 46px; }
        .home-hero-grid, .pathway-grid, .trust-grid, .service-grid, .event-grid, .testimonial-grid, .requirement-grid, .update-grid, .office-grid { grid-template-columns: 1fr; }
        .home-hero-grid { min-height: auto; }
        .home-hero-media { min-height: 290px; }
        .home-footer-bottom { flex-direction: column; }
    }
@endsection

@section('content')
    @php
        $brand = $brand ?? \App\Support\CmsContent::get('brand', config('cms.brand'));
        $page = array_replace_recursive(config('cms.pages.home-v2', []), $page ?? \App\Support\CmsContent::page('home-v2'));
        $services = $services ?? \App\Support\CmsContent::services();
        $pageLinks = \App\Support\CmsContent::get('pages', config('cms.pages', []));
        $serviceCards = array_slice($services, 0, 8, true);
        $pageUrl = function (?string $path): string {
            $path = trim((string) $path);
            if ($path === '') return '#';
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '#')) return $path;
            return url($path);
        };
        $assetUrl = function (?string $path): string {
            $path = trim((string) $path);
            if ($path === '') return asset('hero.jpg');
            return str_starts_with($path, 'http://') || str_starts_with($path, 'https://') ? $path : asset($path);
        };
        $phoneHref = fn (?string $phone): string => 'tel:' . preg_replace('/\D+/', '', (string) $phone);
        $introParagraphs = array_values(array_filter(array_map('trim', explode("\n\n", str_replace(["\r\n", "\r"], "\n", $page['intro_text'] ?? '')))));
        $facts = $page['intro_facts'] ?? [];
        $pathways = $page['pathways'] ?? [];
        $trust = $page['trust_items'] ?? [];
        $events = $page['events'] ?? [];
        $testimonials = $page['testimonials'] ?? [];
        $requirements = $page['requirements'] ?? [];
        $updates = $page['updates'] ?? [];
        $locations = $page['locations'] ?? [];
        $offices = $page['offices'] ?? [];
        $serviceFooterLinks = array_slice($services, 0, 3, true);
    @endphp

    <section class="home-hero">
        <div class="home-hero-grid">
            <div class="home-hero-copy">
                <h1>{{ $page['hero_title'] ?? 'Our Care' }}</h1>
                <p>{{ $page['hero_subtitle'] ?? '' }}</p>
                @if(!empty($page['hero_cta_label']))
                    <a class="home-btn" href="{{ $pageUrl($page['hero_cta_url'] ?? '') }}">{{ $page['hero_cta_label'] }}</a>
                @endif
                @if(!empty($page['hero_note']))
                    <small class="home-note">{{ $page['hero_note'] }}</small>
                @endif
            </div>
            <div class="home-hero-media"><img src="{{ $assetUrl($page['hero_image'] ?? 'hero.jpg') }}" alt="Our Care support worker assisting a participant"></div>
        </div>
    </section>

    <section class="home-section">
        <div class="home-wrap">
            <div class="home-heading"><h2>{{ $page['intro_title'] ?? '' }}</h2></div>
            <div class="intro-grid">
                <div class="intro-copy">
                    @foreach($introParagraphs as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
                <ul class="fact-list">
                    @foreach($facts as $fact)
                        <li>{{ $fact }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="pathway-grid">
                @foreach($pathways as $pathway)
                    <a class="image-card" href="{{ $pageUrl($pathway['url'] ?? '') }}"><img src="{{ $assetUrl($pathway['image'] ?? 'hero.jpg') }}" alt="{{ $pathway['title'] ?? 'Our Care pathway' }}"><span class="image-card-body"><h3>{{ $pathway['title'] ?? '' }}</h3><p>{{ $pathway['text'] ?? '' }}</p></span></a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="home-section pink">
        <div class="home-wrap">
            <div class="home-heading"><h2>{{ $page['trust_heading'] ?? '' }}</h2><p>{{ $page['trust_text'] ?? '' }}</p></div>
            <div class="trust-grid">
                @foreach($trust as $item)
                    <article class="trust-card"><span class="trust-icon">{{ $item['icon'] }}</span><h3>{{ $item['title'] }}</h3><p>{{ $item['text'] }}</p></article>
                @endforeach
            </div>
            @if(!empty($page['rating_text']))
                <div class="rating-card"><div class="stars" aria-label="Five star rating">*****</div><p>{{ $page['rating_text'] }}</p></div>
            @endif
            @if(!empty($page['rating_cta_label']))
                <div class="home-heading" style="margin-bottom: 0;"><a class="home-btn" href="{{ $pageUrl($page['rating_cta_url'] ?? '') }}">{{ $page['rating_cta_label'] }}</a></div>
            @endif
        </div>
    </section>

    <section class="home-section">
        <div class="home-wrap">
            <div class="home-heading"><h2>{{ $page['services_heading'] ?? '' }}</h2></div>
            <div class="service-grid">
                @foreach($serviceCards as $slug => $service)
                    <a class="service-tile" href="{{ route('services.detail.v2', $slug) }}"><span class="service-tile-image"><img src="{{ $assetUrl($service['image'] ?? 'hero.jpg') }}" alt="{{ $service['label'] }}"></span><span>{{ $service['label'] }}</span></a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="home-section warm">
        <div class="home-wrap">
            <div class="home-heading"><h2>{{ $page['events_heading'] ?? '' }}</h2></div>
            <div class="event-grid">
                @foreach($events as $event)
                    <article class="event-card"><div class="event-poster"><img src="{{ $assetUrl($event['image'] ?? 'hero.jpg') }}" alt="{{ $event['title'] ?? 'Our Care event' }}"><div class="event-poster-copy"><small>{{ $event['kicker'] ?? '' }}</small><strong>{{ $event['poster_title'] ?? '' }}</strong></div></div><h3>{{ $event['title'] ?? '' }}</h3><p>{{ $event['meta'] ?? '' }}</p><a class="home-btn" href="{{ $pageUrl($event['url'] ?? '') }}">{{ $event['button_label'] ?? 'Register now' }}</a></article>
                @endforeach
            </div>
            <div class="home-heading" style="margin-top: 64px;"><h2>{{ $page['testimonials_heading'] ?? '' }}</h2><p>{{ $page['testimonials_text'] ?? '' }}</p></div>
            <div class="testimonial-grid">
                @foreach($testimonials as $testimonial)
                    <article class="testimonial-card"><div class="stars">*****</div><p>{{ $testimonial['text'] ?? '' }}</p><strong>{{ $testimonial['author'] ?? '' }}</strong></article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="home-section">
        <div class="home-wrap">
            <div class="home-heading"><h2>{{ $page['requirements_heading'] ?? '' }}</h2></div>
            <div class="requirement-grid">
                @foreach($requirements as $requirement)
                    <article class="requirement-card"><h3>{{ $requirement['title'] ?? '' }}</h3><p>{{ $requirement['text'] ?? '' }}</p><ul class="check-list">@foreach(($requirement['items'] ?? []) as $item)<li>{{ $item }}</li>@endforeach</ul><a class="home-btn" href="{{ $pageUrl($requirement['url'] ?? '') }}">{{ $requirement['button_label'] ?? '' }}</a></article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="home-section soft" id="ndis">
        <div class="home-wrap">
            <div class="home-heading"><h2>{{ $page['updates_heading'] ?? '' }}</h2></div>
            <div class="update-grid">
                @foreach($updates as $update)
                    <article class="update-card"><img src="{{ $assetUrl($update['image'] ?? 'hero.jpg') }}" alt="{{ $update['title'] ?? 'Our Care update' }}"><div class="update-card-body"><h3>{{ $update['title'] ?? '' }}</h3><p>{{ $update['text'] ?? '' }}</p><a class="text-link" href="{{ $pageUrl($update['url'] ?? '') }}">{{ $update['link_label'] ?? 'Read more' }}</a></div></article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="cta-band"><img src="{{ $assetUrl($page['cta_image'] ?? 'contact.jpg') }}" alt="Our Care participants and support workers outdoors"><div class="cta-content"><h2>{{ $page['cta_title'] ?? '' }}</h2><p>{{ $page['cta_text'] ?? '' }}</p><a class="home-btn" href="{{ $pageUrl($page['cta_url'] ?? '') }}">{{ $page['cta_button_label'] ?? '' }}</a></div></section>

    <section class="home-section">
        <div class="home-wrap">
            <div class="home-heading"><h2>{{ $page['office_heading'] ?? '' }}</h2><p>{{ $page['office_text'] ?? '' }}</p><div class="location-pills">@foreach($locations as $location)<span>{{ $location }}</span>@endforeach</div></div>
            <div class="office-grid">
                @foreach($offices as $office)
                    <article class="office-card"><h3>{{ $office['title'] ?? '' }}</h3><p>{{ $office['text'] ?? '' }}</p><p><a class="text-link" href="mailto:{{ $office['email'] ?? '' }}">{{ $office['email'] ?? '' }}</a></p><p><a class="text-link" href="{{ $phoneHref($office['phone'] ?? '') }}">{{ $office['phone'] ?? '' }}</a></p></article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="home-footer">
        <div class="home-footer-grid">
            <div><img src="{{ $assetUrl($brand['logo'] ?? 'logo3.png') }}" alt="Our Care logo"><p>{{ $page['footer_text'] ?? '' }}</p></div>
            <div><h3>{{ $brand['footer_services_label'] ?? 'Services' }}</h3><a href="{{ url('/services-v2') }}">{{ $pageLinks['services-v2']['label'] ?? 'Services' }}</a>@foreach($serviceFooterLinks as $slug => $service)<a href="{{ route('services.detail.v2', $slug) }}">{{ $service['label'] ?? $service['title'] ?? $slug }}</a>@endforeach</div>
            <div><h3>{{ $brand['footer_quick_links_label'] ?? 'Quick Links' }}</h3><a href="{{ url('/about-v2') }}">{{ ($pageLinks['about-v2']['label'] ?? null) ?: 'About Us' }}</a><a href="{{ url('/intake-v2') }}">{{ ($pageLinks['intake-v2']['label'] ?? null) ?: 'Intake' }}</a><a href="{{ url('/onboarding-v2') }}">{{ ($pageLinks['onboarding-v2']['label'] ?? null) ?: 'Onboarding' }}</a><a href="{{ url('/contact-v2') }}">{{ ($pageLinks['contact-v2']['label'] ?? null) ?: 'Contact Us' }}</a></div>
            <div><h3>{{ $brand['footer_contact_label'] ?? 'Contact' }}</h3>@if(!empty($brand['phone']))<a href="{{ $phoneHref($brand['phone']) }}">{{ $brand['phone'] }}</a>@elseif(!empty($offices[0]['phone']))<a href="{{ $phoneHref($offices[0]['phone']) }}">{{ $offices[0]['phone'] }}</a>@endif @if(!empty($brand['email']))<a href="mailto:{{ $brand['email'] }}">{{ $brand['email'] }}</a>@elseif(!empty($offices[0]['email']))<a href="mailto:{{ $offices[0]['email'] }}">{{ $offices[0]['email'] }}</a>@endif<p>{{ implode(', ', $locations) }}</p></div>
        </div>
        <div class="home-footer-bottom"><span>Copyright &copy; {{ date('Y') }} {{ $brand['site_name'] ?? 'Our Care Pty Ltd' }}.</span><span>{{ $page['footer_credit'] ?? '' }}</span></div>
    </section>

    <a class="chat-button" href="{{ url('/contact-v2') }}" aria-label="Contact Our Care">?</a>
@endsection
