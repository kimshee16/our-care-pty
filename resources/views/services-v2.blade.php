@extends('layouts.public-v2', ['activePage' => 'services'])

@section('title', 'Services - Our Care')

@section('styles')
    .services-hero__inner {
        display: grid;
        grid-template-columns: minmax(0, 0.95fr) minmax(320px, 1.05fr);
        gap: 52px;
        align-items: center;
    }

    .services-hero h1 {
        margin-bottom: 22px;
        color: var(--brand);
        font-size: clamp(2.5rem, 5vw, 4.35rem);
        line-height: 1.08;
        letter-spacing: 0;
    }

    .services-hero p {
        max-width: 650px;
        color: #4d5e68;
        font-size: 16px;
    }

    .hero-media {
        overflow: hidden;
        min-height: 320px;
        border-radius: 8px;
        background: var(--brand-soft);
        box-shadow: var(--shadow);
    }

    .hero-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .update-band {
        padding: 28px var(--pad);
        color: #ffffff;
        background: var(--brand);
    }

    .update-band__inner {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 24px;
        align-items: center;
    }

    .update-band h2 {
        margin: 0;
        color: #ffffff;
        font-size: 18px;
        line-height: 1.45;
    }

    .update-band p {
        margin: 8px 0 0;
        color: rgba(255, 255, 255, 0.78);
        font-size: 13px;
    }

    .service-menu {
        padding: 26px var(--pad);
        border-bottom: 1px solid var(--line);
        background: #ffffff;
    }

    .service-menu__inner {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .service-menu a {
        display: inline-flex;
        align-items: center;
        min-height: 38px;
        padding: 9px 12px;
        border: 1px solid rgba(36, 124, 159, 0.18);
        border-radius: 6px;
        color: var(--brand-dark);
        background: var(--brand-soft);
        font-size: 13px;
        font-weight: 800;
    }

    .service-menu a:hover {
        color: #ffffff;
        background: var(--brand);
    }

    .section-heading {
        max-width: 780px;
        margin: 0 auto 48px;
        text-align: center;
    }

    .section-heading h2 {
        margin-bottom: 12px;
        color: var(--ink);
        font-size: clamp(1.9rem, 4vw, 2.7rem);
        line-height: 1.16;
    }

    .services-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 34px 26px;
    }

    .service-card {
        display: grid;
        grid-template-rows: 220px auto;
        overflow: hidden;
        min-height: 100%;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #ffffff;
        box-shadow: 0 12px 28px rgba(20, 60, 78, 0.07);
    }

    .service-card__media {
        display: grid;
        place-items: center;
        color: var(--brand);
        background:
            linear-gradient(135deg, rgba(36, 124, 159, 0.08), rgba(127, 199, 177, 0.12)),
            #fbfdfe;
    }

    .service-card:nth-child(3n + 2) .service-card__media {
        color: #508b72;
        background:
            linear-gradient(135deg, rgba(127, 199, 177, 0.16), rgba(243, 201, 105, 0.13)),
            #fbfdfe;
    }

    .service-card:nth-child(3n) .service-card__media {
        color: #c76550;
        background:
            linear-gradient(135deg, rgba(231, 119, 98, 0.14), rgba(36, 124, 159, 0.08)),
            #fbfdfe;
    }

    .service-card__body {
        display: grid;
        padding: 26px 24px 24px;
    }

    .service-card h3 {
        margin: 0 0 16px;
        color: var(--brand);
        text-align: center;
        font-size: 24px;
        line-height: 1.2;
    }

    .service-card p {
        margin-bottom: 16px;
        color: #34444d;
        font-size: 14px;
    }

    .service-card strong {
        color: #111d24;
    }

    .service-card .button {
        width: 100%;
        margin-top: auto;
    }

    @media (max-width: 980px) {
        .services-hero__inner,
        .update-band__inner {
            grid-template-columns: 1fr;
        }

        .services-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .service-menu {
            padding-left: 20px;
            padding-right: 20px;
        }

        .services-grid {
            grid-template-columns: 1fr;
        }

        .hero-media {
            min-height: 230px;
        }
    }
@endsection

@section('content')
    <section class="section section--soft services-hero">
        <div class="container services-hero__inner">
            <div>
                <h1>Support Services Designed Around Everyday Life</h1>
                <p>Our Care offers flexible, person-centred support designed to make everyday life safer, easier, and more connected.</p>
            </div>
            <div class="hero-media">
                <img src="{{ asset('hero.jpg') }}" alt="Support worker assisting an older client outdoors">
            </div>
        </div>
    </section>

    <section class="update-band" id="ndis">
        <div class="container update-band__inner">
            <div>
                <h2>01 Jul 2026 imposing financial limits for categories including personal care, specialised nursing, and supported accommodation.</h2>
                <p>Review service categories and supports before planning your next care arrangement.</p>
            </div>
            <a class="button button--light" href="#services-list">Read More</a>
        </div>
    </section>

    <aside class="service-menu" aria-label="Services submenu">
        <div class="container service-menu__inner">
            @foreach(config('ourcare_v2.services') as $slug => $service)
                <a href="{{ route('services.detail.v2', $slug) }}">{{ $service['label'] }}</a>
            @endforeach
        </div>
    </aside>

    <section class="section" id="services-list">
        <div class="container">
            <div class="section-heading">
                <h2>Support That Helps You Live Life Your Way</h2>
                <p>Explore Our Care's NDIS support services and choose the right pathway for your needs.</p>
            </div>

            <div class="services-grid">
                @foreach(config('ourcare_v2.services') as $slug => $service)
                    <article class="service-card" id="{{ $slug }}">
                        <div class="service-card__media" aria-hidden="true">
                            <svg width="74" height="74" viewBox="0 0 24 24" fill="none">
                                <path d="M12 3.5 20 8v10.5H4V8l8-4.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M8 13.5h8M12 9.5v8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div class="service-card__body">
                            <h3>{{ $service['label'] }}</h3>
                            <p><strong>{{ $service['registration'] }}</strong></p>
                            <p>{{ $service['summary'] }}</p>
                            <a class="button" href="{{ route('services.detail.v2', $slug) }}">{{ $service['label'] }}</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection
