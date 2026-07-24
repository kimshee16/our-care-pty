@extends('layouts.public-v2', ['activePage' => 'onboarding'])

@section('title', 'Onboarding - Our Care')

@section('styles')
    .hero-row,
    .split-row {
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.95fr);
        gap: 48px;
        align-items: center;
    }

    .hero-row h1,
    .split-row h2 {
        margin-bottom: 22px;
        color: var(--brand);
        font-size: clamp(2.5rem, 5vw, 4.35rem);
        line-height: 1.08;
        letter-spacing: 0;
    }

    .image-panel {
        overflow: hidden;
        min-height: 300px;
        border-radius: 8px;
        background: var(--brand-soft);
        box-shadow: var(--shadow);
    }

    .image-panel img {
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

    .section-heading {
        max-width: 780px;
        margin: 0 auto 44px;
        text-align: center;
    }

    .section-heading h2 {
        margin-bottom: 12px;
        color: var(--ink);
        font-size: clamp(1.9rem, 4vw, 2.7rem);
        line-height: 1.16;
    }

    .support-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 24px;
    }

    .support-card {
        display: grid;
        grid-template-rows: 230px auto;
        overflow: hidden;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #ffffff;
        box-shadow: 0 12px 28px rgba(20, 60, 78, 0.07);
    }

    .support-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .support-card__body {
        display: grid;
        justify-items: center;
        padding: 24px;
        text-align: center;
    }

    .support-card h3 {
        margin: 0 0 12px;
        color: var(--brand);
        font-size: 26px;
        line-height: 1.25;
    }

    .split-row {
        padding: 34px 0;
        border-top: 1px solid var(--line);
    }

    .split-row:first-child {
        padding-top: 0;
        border-top: 0;
    }

    .split-row .button {
        margin-top: 22px;
        width: min(100%, 480px);
    }

    @media (max-width: 900px) {
        .hero-row,
        .update-band__inner,
        .support-grid,
        .split-row {
            grid-template-columns: 1fr;
        }
    }
@endsection

@section('content')
    <section class="section section--soft">
        <div class="container hero-row">
            <div>
                <h1>Be a Support Worker</h1>
                <p>Changes to NDIS means you will work with well-informed disability services in Australia. Our onboarding pathway helps workers and clients begin with clarity, confidence, and the right documentation.</p>
            </div>
            <div class="image-panel">
                <img src="{{ asset('ready.jpg') }}" alt="Support worker onboarding">
            </div>
        </div>
    </section>

    <section class="update-band">
        <div class="container update-band__inner">
            <h2>01 Jul 2026 imposing financial limits for categories including personal care, specialised nursing, and supported accommodation.</h2>
            <a class="button button--light" href="{{ url('/services-v2') }}">Read More</a>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-heading">
                <h2>Support That Helps You Live Life Your Way</h2>
                <p>Our onboarding flow keeps the essentials simple while giving every participant and worker a dependable starting point.</p>
            </div>

            <div class="support-grid">
                <article class="support-card">
                    <img src="{{ asset('hero.jpg') }}" alt="Person-centred support">
                    <div class="support-card__body">
                        <h3>Person-Centred Support</h3>
                        <p>Support tailored around your needs, preferences and routines while achieving goals.</p>
                        <a class="button" href="{{ url('/intake-v2') }}">Book a Consultation</a>
                    </div>
                </article>
                <article class="support-card">
                    <img src="{{ asset('contact.jpg') }}" alt="Peace of mind for families">
                    <div class="support-card__body">
                        <h3>Peace of Mind for Families</h3>
                        <p>Helping individuals, families, and carers feel more supported every day with transparency and accountability.</p>
                        <a class="button" href="{{ url('/intake-v2') }}">Book a Consultation</a>
                    </div>
                </article>
                <article class="support-card">
                    <img src="{{ asset('ready.jpg') }}" alt="Reliable care from experts">
                    <div class="support-card__body">
                        <h3>Reliable Care from Experts</h3>
                        <p>Professional networks that work closely with participants, families, healthcare providers, and support teams.</p>
                        <a class="button" href="{{ url('/intake-v2') }}">Book a Consultation</a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <article class="split-row">
                <div>
                    <h2>Hire a Worker</h2>
                    <p>Check qualifications, describe your support needs, and start intake for community support matched to your goals.</p>
                    <a class="button" href="{{ url('/client-register') }}">Create Your Account</a>
                </div>
                <div class="image-panel">
                    <img src="{{ asset('hero.jpg') }}" alt="Hire a worker">
                </div>
            </article>
            <article class="split-row">
                <div>
                    <h2>Be a Support Worker</h2>
                    <p>We value your qualification. Join our network of community advocating professional care and build a trusted profile.</p>
                    <a class="button" href="{{ url('/healthcare-register') }}">Create Your Account</a>
                </div>
                <div class="image-panel">
                    <img src="{{ asset('ready.jpg') }}" alt="Become a support worker">
                </div>
            </article>
        </div>
    </section>
@endsection
