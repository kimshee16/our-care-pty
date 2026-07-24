@extends('layouts.public-v2', ['activePage' => 'home'])

@section('title', 'Our Care - Home V2')

@section('styles')
    .hero-title {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 18px;
        margin-bottom: 46px;
        color: #5f9ec0;
        text-align: center;
    }

    .hero-title img {
        width: 74px;
        height: 74px;
        object-fit: contain;
    }

    .hero-title h1 {
        margin: 0;
        font-size: clamp(2.3rem, 5vw, 4.5rem);
        font-weight: 300;
        line-height: 1.08;
        letter-spacing: 0;
    }

    .hero-grid,
    .quick-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 24px;
    }

    .hero-card,
    .quick-card,
    .service-preview {
        overflow: hidden;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #ffffff;
        box-shadow: 0 12px 28px rgba(20, 60, 78, 0.07);
    }

    .hero-card {
        display: grid;
        grid-template-rows: 245px auto;
    }

    .hero-card img,
    .feature-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .hero-card__body {
        display: grid;
        justify-items: center;
        padding: 24px;
        text-align: center;
    }

    .hero-card h2 {
        margin: 0 0 10px;
        color: var(--brand);
        font-size: 24px;
        line-height: 1.2;
    }

    .hero-card p,
    .quick-card p,
    .service-preview p {
        margin-bottom: 18px;
    }

    .feature-row {
        display: grid;
        grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
        gap: 34px;
        align-items: center;
    }

    .feature-row h2,
    .section-heading h2 {
        margin-bottom: 14px;
        color: var(--brand);
        font-size: clamp(2rem, 4vw, 3rem);
        line-height: 1.12;
    }

    .feature-image {
        overflow: hidden;
        min-height: 330px;
        border-radius: 8px;
        background: var(--brand-soft);
        box-shadow: var(--shadow);
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

    .section-heading {
        max-width: 780px;
        margin: 0 auto 42px;
        text-align: center;
    }

    .preview-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 24px;
    }

    .service-preview {
        display: grid;
        grid-template-rows: 180px auto;
    }

    .service-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .service-preview__body {
        display: grid;
        justify-items: center;
        padding: 22px;
        text-align: center;
    }

    .service-preview h3 {
        margin: 0 0 10px;
        color: var(--brand);
        font-size: 22px;
        line-height: 1.25;
    }

    @media (max-width: 900px) {
        .hero-grid,
        .feature-row,
        .preview-grid,
        .update-band__inner {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .hero-title {
            flex-direction: column;
        }

        .hero-card {
            grid-template-rows: 210px auto;
        }
    }
@endsection

@section('content')
    <section class="section section--soft">
        <div class="container">
            <div class="hero-title">
                <img src="{{ asset('logo2.png') }}" alt="Our Care logo mark">
                <h1>Our Care Your Wellness</h1>
            </div>

            <div class="hero-grid">
                <article class="hero-card">
                    <img src="{{ asset('hero.jpg') }}" alt="Support worker assisting a client">
                    <div class="hero-card__body">
                        <h2>Hire trusted community support</h2>
                        <p>Find qualified workers for personal care, community access, domestic support, transport and daily living needs.</p>
                        <a class="button" href="{{ url('/client-register') }}">Start Client Intake</a>
                    </div>
                </article>

                <article class="hero-card">
                    <img src="{{ asset('ready.jpg') }}" alt="Care team preparing support">
                    <div class="hero-card__body">
                        <h2>Join as a core worker</h2>
                        <p>Show your qualifications, build your profile, and connect with families looking for dependable support.</p>
                        <a class="button" href="{{ url('/healthcare-register') }}">Apply as Worker</a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container feature-row">
            <div>
                <p class="eyebrow">Our community support</p>
                <h2>Support that meets people where they are</h2>
                <p>Our Care helps participants, families and carers connect with dependable workers for everyday living, community participation and goal-focused NDIS support.</p>
            </div>
            <div class="feature-image">
                <img src="{{ asset('contact.jpg') }}" alt="Our Care community support">
            </div>
        </div>
    </section>

    <section class="update-band" id="ndis">
        <div class="container update-band__inner">
            <div>
                <h2>01 Jul 2026 imposing financial limits for categories including personal care, specialised nursing, and supported accommodation.</h2>
                <p>Keep care planning current with NDIS pricing and service updates.</p>
            </div>
            <a class="button button--light" href="{{ url('/services-v2') }}">Read More</a>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-heading">
                <h2>Support That Helps You Live Life Your Way</h2>
                <p>Choose practical support from a team focused on reliability, respect and clear communication.</p>
            </div>

            <div class="preview-grid">
                @foreach(array_slice(config('ourcare_v2.services'), 0, 3) as $slug => $service)
                    <article class="service-preview">
                        <img src="{{ asset($service['image']) }}" alt="{{ $service['label'] }}">
                        <div class="service-preview__body">
                            <h3>{{ $service['label'] }}</h3>
                            <p>{{ $service['summary'] }}</p>
                            <a class="button" href="{{ route('services.detail.v2', $slug) }}">Learn More</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection
