@extends('layouts.public-v2', ['activePage' => 'home'])

@section('title', ($page['title'] ?? 'Our Care') . ' - Home')

@section('styles')
    @php
        $palette = array_replace(config('cms.palette', []), $palette ?? \App\Support\CmsContent::get('palette', []));
        $stylePage = array_replace_recursive(config('cms.pages.home-v2', []), $page ?? \App\Support\CmsContent::page('home-v2'));
        $heroBackgroundStart = $stylePage['hero_background_start'] ?: config('cms.pages.home-v2.hero_background_start', '#ffdbe5');
        $heroBackgroundMid = $stylePage['hero_background_mid'] ?: config('cms.pages.home-v2.hero_background_mid', '#ffe4d2');
        $heroBackgroundEnd = $stylePage['hero_background_end'] ?: config('cms.pages.home-v2.hero_background_end', '#fff5cf');
        $fontFamilies = config('cms.font_families', []);
        $typography = $stylePage['typography'] ?? [];
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
        --home-hero-background: linear-gradient(100deg, {{ $heroBackgroundStart }} 0%, {{ $heroBackgroundMid }} 47%, {{ $heroBackgroundEnd }} 100%);
        --home-peach: #ffdfc2;
        --home-blush: #f7c5d8;
        --home-cream: #fff5d6;
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
            .home-typo-{{ $sectionClass }} :where(h1,h2,h3,p,small,a,span,strong,li) {
                @if($familyStack) font-family: {!! $familyStack !!} !important; @endif
                @if($fontSize) font-size: {{ $fontSize }}px !important; @endif
                @if($fontColor) color: {{ $fontColor }} !important; @endif
            }
        @endif
    @endforeach

    body { overflow-x: hidden; background: #fff; }
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
    .submenu { top: calc(100% + 8px); min-width: 320px; padding: 12px; border-radius: 5px; }
    .submenu a { padding: 11px 13px; font-size: 15px; }
    .footer { display: none; }

    .button, .home-btn { display: inline-flex; align-items: center; justify-content: center; min-height: 54px; padding: 16px 25px; border: 0; border-radius: 999px; color: #fff; background: var(--home-orange); box-shadow: none; font-size: 17px; font-weight: 900; line-height: 1; }
    .button:hover, .home-btn:hover { transform: translateY(-1px); box-shadow: 0 10px 20px rgba(255,112,68,.18); }

    .home-wrap { width: min(100%, var(--home-content)); margin: 0 auto; }
    .home-section { padding: 96px var(--pad); background: #fff; }
    .home-section.pink { padding-top: 98px; padding-bottom: 104px; background: linear-gradient(180deg, #f8c6d7 0%, #ffd0bf 100%); }
    .home-section.warm { padding-top: 90px; padding-bottom: 98px; background: linear-gradient(180deg, #ffd9c5 0%, #fff3cf 100%); }
    .home-section.soft { background: #fff; border-top: 1px solid rgba(45,18,75,.08); }
    .home-heading { max-width: 680px; margin: 0 auto 34px; text-align: center; }
    .home-heading h2 { margin: 0 0 18px; color: var(--home-ink); font-size: clamp(2rem, 2.8vw, 2.35rem); line-height: 1.18; }
    .home-heading p { margin: 0; color: color-mix(in srgb, var(--home-ink) 70%, #fff); font-size: 17px; line-height: 1.7; }

    .home-hero { position: relative; overflow: hidden; min-height: 700px; padding: 0 clamp(90px, 9.8vw, 188px); background: var(--home-hero-background); }
    .home-hero-grid { display: grid; grid-template-columns: minmax(560px, 650px) minmax(0,1fr); gap: 64px; align-items: stretch; width: 100%; max-width: var(--home-hero-content); min-height: 700px; margin: 0 auto; }
    .home-hero-copy { position: relative; z-index: 2; align-self: start; padding: 194px 0 24px; }
    .home-hero h1 { max-width: 720px; margin: 0 0 32px; color: var(--home-ink); font-size: 64px; font-weight: 950; line-height: 1.16; overflow-wrap: break-word; letter-spacing: 0; -webkit-text-stroke: .35px currentColor; text-shadow: .25px 0 0 currentColor, 0 .25px 0 currentColor; }
    .home-hero p { max-width: 720px; margin: 0 0 28px; color: #22152f; font-size: 26px; font-weight: 400; line-height: 1.42; }
    .home-note { display: block; max-width: 760px; margin-top: 36px; color: rgba(45,18,75,.9); font-size: 13px; font-weight: 500; line-height: 1.55; }
    .home-hero-media { position: absolute; z-index: 1; inset: 0 0 0 auto; width: min(63vw, 1120px); min-height: 700px; overflow: hidden; pointer-events: none; }
    .home-hero-slide { position: absolute; top: var(--slide-top, auto); right: var(--slide-right, 0); bottom: var(--slide-bottom, 0); width: auto; height: var(--slide-height, 700px); object-fit: contain; object-position: right bottom; border-radius: 0; opacity: 0; filter: drop-shadow(0 24px 34px rgba(45,18,75,.12)); transform: scale(.99); transform-origin: 82% 100%; animation: heroPeopleCarousel 15s cubic-bezier(.2,.78,.22,1) infinite both; will-change: opacity, transform; }
    .home-hero-slide:nth-child(1) { --slide-height: 816px; --slide-top: -94px; --slide-right: -294px; --slide-bottom: auto; animation-delay: -.9s; }
    .home-hero-slide:nth-child(2) { --slide-height: 760px; --slide-top: 0; --slide-right: -16px; --slide-bottom: auto; animation-delay: 4.1s; }
    .home-hero-slide:nth-child(3) { --slide-height: 724px; --slide-right: 0; --slide-bottom: 0; animation-delay: 9.1s; }

    @keyframes heroPeopleCarousel {
        0% { opacity: 0; transform: scale(.99); }
        6%, 28% { opacity: 1; transform: scale(1); }
        34%, 100% { opacity: 0; transform: scale(1.018); }
    }

    @media (prefers-reduced-motion: reduce) {
        .home-hero-slide { animation: none; opacity: 0; transform: none; }
        .home-hero-slide:first-child { opacity: 1; }
    }

    .home-hero-media:has(.home-hero-slide:only-child) .home-hero-slide { animation: none; opacity: 1; transform: none; }

    .intro-grid { display: grid; gap: 14px; width: min(100%, 720px); margin: -8px auto 30px; text-align: center; }
    .intro-copy p { margin: 0 auto 12px; color: color-mix(in srgb, var(--home-ink) 70%, #fff); font-size: 17px; line-height: 1.75; }
    .fact-list { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; padding: 0; margin: 2px auto 0; list-style: none; }
    .fact-list li { padding: 9px 14px; border-radius: 999px; color: var(--home-ink); background: #fff6ec; box-shadow: none; font-size: 13px; font-weight: 850; }

    .pathway-grid, .event-grid, .testimonial-grid, .update-grid, .requirement-grid { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 22px; }
    .pathway-grid { width: min(100%, 640px); margin: 0 auto; gap: 24px; }
    .image-card { position: relative; overflow: hidden; min-height: 170px; padding: 20px 18px; border-radius: 5px; color: #fff; background: var(--home-plum); box-shadow: 0 12px 23px rgba(45,18,75,.18); }
    .image-card img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: .68; }
    .image-card:after { content: ""; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(45,18,75,.16), rgba(45,18,75,.72)); }
    .image-card-body { position: relative; z-index: 1; display: grid; align-content: end; min-height: 130px; }
    .image-card h3 { margin: 0 0 7px; color: #fff; font-size: 17px; line-height: 1.05; text-align: center; }
    .image-card p { margin: 0 auto; max-width: 190px; color: rgba(255,255,255,.9); font-size: 11px; line-height: 1.35; text-align: center; }

    .home-section.pink .home-heading { max-width: 1080px; margin-bottom: 64px; }
    .home-section.pink .home-heading h2 { font-size: 42px; line-height: 1.18; }
    .home-section.pink .home-heading p { font-size: 22px; line-height: 1.6; }
    .trust-grid, .service-grid { display: grid; grid-template-columns: repeat(4,minmax(0,1fr)); gap: 28px 24px; }
    .trust-grid { grid-template-columns: repeat(3,minmax(0,1fr)); width: min(100%, 1120px); margin: 0 auto 58px; gap: 78px 88px; }
    .trust-card { display: grid; justify-items: center; text-align: center; }
    .trust-icon { display: grid; place-items: center; width: 58px; height: 58px; border: 3px solid var(--home-plum); border-radius: 50%; color: var(--home-plum); background: transparent; font-size: 16px; font-weight: 950; line-height: 1; }
    .trust-card h3 { margin: 20px 0 10px; color: var(--home-ink); font-size: 24px; line-height: 1.2; }
    .trust-card p { max-width: 330px; margin: 0; color: #4f3d5b; font-size: 18px; line-height: 1.45; }
    .rating-card, .testimonial-card, .update-card, .requirement-card { border-radius: 5px; background: #fff; box-shadow: 0 10px 24px rgba(45,18,75,.08); }
    .rating-card { width: min(100%, 560px); margin: 0 auto 46px; padding: 34px 42px; text-align: center; }
    .stars { margin-bottom: 12px; color: #ffc107; font-size: 20px; letter-spacing: 0; }
    .rating-card p, .testimonial-card p { margin: 0; color: color-mix(in srgb, var(--home-ink) 68%, #fff); font-size: 16px; line-height: 1.65; }

    .service-grid { width: min(100%, 800px); margin: 0 auto; gap: 28px 34px; }
    .service-tile { display: grid; gap: 8px; color: var(--home-ink); font-weight: 950; text-align: center; }
    .service-tile-image { overflow: hidden; aspect-ratio: 1.05/1; border-radius: 5px; background: #f4edf6; box-shadow: 0 9px 18px rgba(45,18,75,.12); }
    .service-tile-image img { width: 100%; height: 100%; object-fit: cover; transition: transform .25s ease; }
    .service-tile:hover img { transform: scale(1.04); }
    .service-tile span:last-child { min-height: 32px; font-size: 10px; line-height: 1.25; }

    .event-grid { width: min(100%, 660px); margin: 0 auto; gap: 20px; }
    .event-card { display: grid; justify-items: center; gap: 10px; text-align: center; }
    .event-poster { position: relative; overflow: hidden; width: 100%; aspect-ratio: 1.05/1; border-radius: 5px; background: var(--home-plum); box-shadow: 0 12px 22px rgba(45,18,75,.14); }
    .event-poster img { width: 100%; height: 100%; object-fit: cover; opacity: .34; }
    .event-poster-copy { position: absolute; inset: 14px; display: grid; align-content: center; justify-items: center; color: #fff; text-align: center; }
    .event-poster-copy small { margin-bottom: 9px; padding: 5px 8px; border-radius: 999px; color: var(--home-plum); background: #fff; font-size: 9px; font-weight: 950; }
    .event-poster-copy strong { display: block; max-width: 210px; font-size: clamp(.95rem,1.75vw,1.32rem); line-height: 1.08; text-transform: uppercase; }
    .event-card h3 { margin: 0; color: var(--home-ink); font-size: 11px; line-height: 1.35; }
    .event-card p { margin: -3px 0 0; color: color-mix(in srgb, var(--home-ink) 70%, #fff); font-size: 9px; font-weight: 900; text-transform: uppercase; }
    .testimonial-grid { width: min(100%, 820px); margin: 0 auto; }
    .testimonial-card { padding: 28px 23px; min-height: 178px; text-align: center; }
    .testimonial-card strong { display: block; margin-top: 12px; color: var(--home-ink); font-size: 10px; }

    .requirement-grid { width: min(100%, 800px); margin: 0 auto; gap: 24px; }
    .requirement-card { display: grid; align-content: start; min-height: 282px; padding: 26px 22px; box-shadow: 0 12px 24px rgba(45,18,75,.08); }
    .requirement-card:nth-child(1) { background: #d6a9d9; }
    .requirement-card:nth-child(2) { background: #ffc1d7; }
    .requirement-card:nth-child(3) { background: #ffd8ad; }
    .requirement-card h3 { margin: 0 0 8px; color: var(--home-ink); font-size: 20px; line-height: 1.05; text-align: center; }
    .requirement-card p { margin: 0 0 18px; color: #443454; font-size: 10px; line-height: 1.55; }
    .check-list { display: grid; gap: 8px; margin: 0 0 22px; padding: 0; list-style: none; }
    .check-list li { position: relative; padding-left: 22px; color: #332243; font-size: 10px; line-height: 1.45; }
    .check-list li:before { content: ""; position: absolute; left: 0; top: 2px; width: 14px; height: 14px; border-radius: 50%; background: var(--home-orange); }
    .check-list li:after { content: ""; position: absolute; left: 4px; top: 5px; width: 5px; height: 8px; border: solid #fff; border-width: 0 2px 2px 0; transform: rotate(45deg); }
    .requirement-card .home-btn { justify-self: center; align-self: end; margin-top: auto; background: var(--home-plum); }

    .update-grid { width: min(100%, 780px); margin: 0 auto; gap: 24px; }
    .update-card { overflow: hidden; box-shadow: none; }
    .update-card img { width: 100%; aspect-ratio: 1.36/1; border-radius: 4px; object-fit: cover; }
    .update-card-body { padding: 13px 0 0; }
    .update-card h3 { margin: 0 0 8px; color: var(--home-ink); font-size: 12px; line-height: 1.25; }
    .update-card p { margin: 0 0 10px; color: color-mix(in srgb, var(--home-ink) 65%, #fff); font-size: 10px; line-height: 1.6; }
    .text-link { color: var(--home-orange); font-size: 10px; font-weight: 950; }

    .cta-band { position: relative; overflow: hidden; min-height: 320px; padding: 86px var(--pad); color: #fff; background: var(--home-plum); text-align: center; }
    .cta-band img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: .58; }
    .cta-band:after { content: ""; position: absolute; inset: 0; background: rgba(31,14,49,.48); }
    .cta-content { position: relative; z-index: 1; max-width: 760px; margin: 0 auto; }
    .cta-band h2 { margin: 0 0 12px; color: #fff; font-size: clamp(1.55rem,3vw,2rem); line-height: 1.12; }
    .cta-band p { margin: 0 auto 22px; color: rgba(255,255,255,.88); font-size: 12px; line-height: 1.7; }
    .office-grid { display: grid; width: min(100%,760px); margin: 30px auto 0; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 30px; }
    .office-card h3 { margin: 0 0 12px; color: var(--home-ink); font-size: 13px; }
    .office-card p { margin: 0 0 8px; color: color-mix(in srgb, var(--home-ink) 68%, #fff); font-size: 10px; line-height: 1.5; }
    .location-pills { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; margin-top: 18px; }
    .location-pills span { padding: 7px 12px; border-radius: 999px; color: #fff; background: var(--home-orange); font-size: 10px; font-weight: 900; }

    .home-footer { padding: 48px var(--pad) 28px; color: rgba(255,255,255,.78); background: var(--home-plum); }
    .home-footer-grid { display: grid; grid-template-columns: minmax(220px,1.2fr) repeat(3,minmax(140px,1fr)); gap: 36px; width: min(100%, var(--home-content)); margin: 0 auto 32px; }
    .home-footer img { width: 150px; height: auto; padding: 0; border-radius: 0; background: transparent; object-fit: contain; }
    .home-footer h3 { margin: 0 0 32px; color: #fff; font-size: 28px; font-weight: 900; line-height: 1.15; }
    .home-footer a, .home-footer p { display: block; margin: 0 0 24px; color: rgba(255,255,255,.92); font-size: 18px; font-weight: 700; line-height: 1.55; }
    .home-footer-bottom { display: flex; justify-content: space-between; gap: 18px; width: min(100%, var(--home-content)); margin: 0 auto; padding-top: 28px; border-top: 1px solid rgba(255,255,255,.15); color: rgba(255,255,255,.92); font-size: 18px; font-weight: 700; }
    .chat-button { position: fixed; right: 18px; bottom: 18px; z-index: 25; display: grid; place-items: center; width: 38px; height: 38px; border-radius: 50%; color: #fff; background: var(--home-orange); box-shadow: 0 12px 24px rgba(255,112,68,.28); font-size: 18px; font-weight: 900; }

    @media (max-width: 980px) {
        body .site-header { align-items: center; flex-direction: row; flex-wrap: wrap; gap: 8px 18px; }
        body .brand-link { justify-content: flex-start; min-width: auto; }
        body .brand-link img { width: 220px; height: 82px; }
        body .nav-links { flex: 1 1 100%; justify-content: center; gap: 14px; max-width: 100%; overflow: hidden; }
        .submenu { display: none !important; }
        .home-hero-grid, .intro-grid, .home-footer-grid { grid-template-columns: 1fr; }
        .home-hero { padding-bottom: 0; }
        .home-hero-copy { padding-bottom: 0; }
        .home-hero-media { position: relative; inset: auto; width: 100%; min-height: 340px; }
        .home-hero-slide { height: var(--slide-height, 340px); }
        .home-hero-slide:nth-child(1) { --slide-height: 398px; --slide-top: -46px; --slide-right: -143px; --slide-bottom: auto; }
        .home-hero-slide:nth-child(2) { --slide-height: 370px; --slide-top: 0; --slide-right: -8px; --slide-bottom: auto; }
        .home-hero-slide:nth-child(3) { --slide-height: 352px; --slide-right: 0; --slide-bottom: 0; }
        .pathway-grid, .trust-grid, .service-grid, .event-grid, .testimonial-grid, .requirement-grid, .update-grid { grid-template-columns: repeat(2,minmax(0,1fr)); }
        .pathway-grid, .event-grid, .service-grid, .testimonial-grid, .requirement-grid, .update-grid { width: min(100%, 680px); }
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
        .home-section, .home-section.pink, .home-section.warm { padding-top: 54px; padding-bottom: 58px; }
        .home-heading { max-width: 300px; }
        .home-heading h2 { font-size: 1.18rem; }
        .home-hero { min-height: auto; padding: 38px 20px 0; }
        .home-hero-grid, .pathway-grid, .trust-grid, .service-grid, .event-grid, .testimonial-grid, .requirement-grid, .update-grid, .office-grid { grid-template-columns: 1fr; }
        .home-wrap, .home-hero-grid { width: 100%; min-width: 0; max-width: 100%; }
        .home-hero-grid { display: block; min-height: auto; }
        .home-hero-copy, .home-hero h1, .home-hero p, .home-note { width: 100%; min-width: 0; max-width: 310px; }
        .intro-grid { width: 100%; min-width: 0; max-width: 280px; }
        .home-hero h1 { font-size: 1.78rem; line-height: 1.06; }
        .home-hero p { font-size: 12px; }
        .intro-copy p { font-size: 10px; }
        .fact-list li { max-width: 100%; font-size: 8px; text-align: center; white-space: normal; }
        .home-hero-media { min-height: 280px; }
        .home-hero-slide { height: var(--slide-height, 280px); }
        .home-hero-slide:nth-child(1) { --slide-height: 327px; --slide-top: -38px; --slide-right: -118px; --slide-bottom: auto; }
        .home-hero-slide:nth-child(2) { --slide-height: 305px; --slide-top: 0; --slide-right: -7px; --slide-bottom: auto; }
        .home-hero-slide:nth-child(3) { --slide-height: 292px; --slide-right: 0; --slide-bottom: 0; }
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
        $heroSlides = array_values(array_filter($page['hero_slides'] ?? [], fn ($slide) => !empty($slide['image'] ?? '')));
        if (empty($heroSlides) && !empty($page['hero_image'])) {
            $heroSlides = [['image' => $page['hero_image'], 'alt' => $page['hero_title'] ?? config('cms.pages.home-v2.hero_title')]];
        }
        $trust = $page['trust_items'] ?? [];
        $events = $page['events'] ?? [];
        $testimonials = $page['testimonials'] ?? [];
        $requirements = $page['requirements'] ?? [];
        $updates = $page['updates'] ?? [];
        $locations = $page['locations'] ?? [];
        $offices = $page['offices'] ?? [];
        $serviceFooterLinks = array_slice($services, 0, 3, true);
    @endphp

    <section class="home-hero home-typo-hero">
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
            <div class="home-hero-media">
                @foreach(array_slice($heroSlides, 0, 3) as $slide)
                    <img class="home-hero-slide" src="{{ $assetUrl($slide['image'] ?? '') }}" alt="{{ $slide['alt'] ?? ($page['hero_title'] ?? config('cms.pages.home-v2.hero_title')) }}">
                @endforeach
            </div>
        </div>
    </section>

    <section class="home-section">
        <div class="home-wrap">
            <div class="home-heading home-typo-intro"><h2>{{ $page['intro_title'] ?? '' }}</h2></div>
            <div class="intro-grid home-typo-intro">
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
            <div class="pathway-grid home-typo-pathways">
                @foreach($pathways as $pathway)
                    <a class="image-card" href="{{ $pageUrl($pathway['url'] ?? '') }}"><img src="{{ $assetUrl($pathway['image'] ?? 'hero.jpg') }}" alt="{{ $pathway['title'] ?? 'Our Care pathway' }}"><span class="image-card-body"><h3>{{ $pathway['title'] ?? '' }}</h3><p>{{ $pathway['text'] ?? '' }}</p></span></a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="home-section pink home-typo-trust">
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

    <section class="home-section home-typo-services">
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
            <div class="home-heading home-typo-events"><h2>{{ $page['events_heading'] ?? '' }}</h2></div>
            <div class="event-grid home-typo-events">
                @foreach($events as $event)
                    <article class="event-card"><div class="event-poster"><img src="{{ $assetUrl($event['image'] ?? 'hero.jpg') }}" alt="{{ $event['title'] ?? config('cms.pages.home-v2.events.0.title') }}"><div class="event-poster-copy"><small>{{ $event['kicker'] ?? '' }}</small><strong>{{ $event['poster_title'] ?? '' }}</strong></div></div><h3>{{ $event['title'] ?? '' }}</h3><p>{{ $event['meta'] ?? '' }}</p><a class="home-btn" href="{{ $pageUrl($event['url'] ?? '') }}">{{ $event['button_label'] ?? config('cms.pages.home-v2.events.0.button_label') }}</a></article>
                @endforeach
            </div>
            <div class="home-heading home-typo-testimonials" style="margin-top: 64px;"><h2>{{ $page['testimonials_heading'] ?? '' }}</h2><p>{{ $page['testimonials_text'] ?? '' }}</p></div>
            <div class="testimonial-grid home-typo-testimonials">
                @foreach($testimonials as $testimonial)
                    <article class="testimonial-card"><div class="stars">*****</div><p>{{ $testimonial['text'] ?? '' }}</p><strong>{{ $testimonial['author'] ?? '' }}</strong></article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="home-section home-typo-requirements">
        <div class="home-wrap">
            <div class="home-heading"><h2>{{ $page['requirements_heading'] ?? '' }}</h2></div>
            <div class="requirement-grid">
                @foreach($requirements as $requirement)
                    <article class="requirement-card"><h3>{{ $requirement['title'] ?? '' }}</h3><p>{{ $requirement['text'] ?? '' }}</p><ul class="check-list">@foreach(($requirement['items'] ?? []) as $item)<li>{{ $item }}</li>@endforeach</ul><a class="home-btn" href="{{ $pageUrl($requirement['url'] ?? '') }}">{{ $requirement['button_label'] ?? '' }}</a></article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="home-section soft home-typo-updates" id="ndis">
        <div class="home-wrap">
            <div class="home-heading"><h2>{{ $page['updates_heading'] ?? '' }}</h2></div>
            <div class="update-grid">
                @foreach($updates as $update)
                    <article class="update-card"><img src="{{ $assetUrl($update['image'] ?? 'hero.jpg') }}" alt="{{ $update['title'] ?? 'Our Care update' }}"><div class="update-card-body"><h3>{{ $update['title'] ?? '' }}</h3><p>{{ $update['text'] ?? '' }}</p><a class="text-link" href="{{ $pageUrl($update['url'] ?? '') }}">{{ $update['link_label'] ?? 'Read more' }}</a></div></article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="cta-band home-typo-cta"><img src="{{ $assetUrl($page['cta_image'] ?? 'contact.jpg') }}" alt="Our Care participants and support workers outdoors"><div class="cta-content"><h2>{{ $page['cta_title'] ?? '' }}</h2><p>{{ $page['cta_text'] ?? '' }}</p><a class="home-btn" href="{{ $pageUrl($page['cta_url'] ?? '') }}">{{ $page['cta_button_label'] ?? '' }}</a></div></section>

    <section class="home-section home-typo-offices">
        <div class="home-wrap">
            <div class="home-heading"><h2>{{ $page['office_heading'] ?? '' }}</h2><p>{{ $page['office_text'] ?? '' }}</p><div class="location-pills">@foreach($locations as $location)<span>{{ $location }}</span>@endforeach</div></div>
            <div class="office-grid">
                @foreach($offices as $office)
                    <article class="office-card"><h3>{{ $office['title'] ?? '' }}</h3><p>{{ $office['text'] ?? '' }}</p><p><a class="text-link" href="mailto:{{ $office['email'] ?? '' }}">{{ $office['email'] ?? '' }}</a></p><p><a class="text-link" href="{{ $phoneHref($office['phone'] ?? '') }}">{{ $office['phone'] ?? '' }}</a></p></article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="home-footer home-typo-footer">
        <div class="home-footer-grid">
            <div><img src="{{ $assetUrl($brand['logo'] ?? 'logo3.png') }}" alt="Our Care logo"><p>{{ $page['footer_text'] ?? '' }}</p></div>
            <div><h3>{{ $brand['footer_services_label'] ?? 'Services' }}</h3><a href="{{ url('/services-v2') }}">{{ $pageLinks['services-v2']['label'] ?? 'Services' }}</a>@foreach($serviceFooterLinks as $slug => $service)<a href="{{ route('services.detail.v2', $slug) }}">{{ $service['label'] ?? $service['title'] ?? $slug }}</a>@endforeach</div>
            <div><h3>{{ $brand['footer_quick_links_label'] ?? 'Quick Links' }}</h3><a href="{{ url('/about-v2') }}">{{ ($pageLinks['about-v2']['label'] ?? null) ?: 'About Us' }}</a><a href="{{ url('/intake-v2') }}">{{ ($pageLinks['intake-v2']['label'] ?? null) ?: 'Intake' }}</a><a href="{{ url('/onboarding-v2') }}">{{ ($pageLinks['onboarding-v2']['label'] ?? null) ?: 'Onboarding' }}</a><a href="{{ url('/contact-v2') }}">{{ ($pageLinks['contact-v2']['label'] ?? null) ?: 'Contact Us' }}</a></div>
            <div><h3>{{ $brand['footer_contact_label'] ?? 'Contact' }}</h3>@if(!empty($brand['phone']))<a href="{{ $phoneHref($brand['phone']) }}">{{ $brand['phone'] }}</a>@elseif(!empty($offices[0]['phone']))<a href="{{ $phoneHref($offices[0]['phone']) }}">{{ $offices[0]['phone'] }}</a>@endif @if(!empty($brand['email']))<a href="mailto:{{ $brand['email'] }}">{{ $brand['email'] }}</a>@elseif(!empty($offices[0]['email']))<a href="mailto:{{ $offices[0]['email'] }}">{{ $offices[0]['email'] }}</a>@endif<p>{{ implode(', ', $locations) }}</p></div>
        </div>
        <div class="home-footer-bottom"><span>Copyright &copy; {{ date('Y') }} {{ $brand['site_name'] ?? 'Our Care Pty Ltd' }}.</span><span>{{ $page['footer_credit'] ?? '' }}</span></div>
    </section>

    <a class="chat-button" href="{{ $pageUrl($brand['chat_url'] ?? config('cms.brand.chat_url')) }}" aria-label="{{ $brand['chat_aria_label'] ?? config('cms.brand.chat_aria_label') }}">{{ $brand['chat_label'] ?? config('cms.brand.chat_label') }}</a>
@endsection
