@extends('layouts.public-v2', ['activePage' => 'intake'])

@section('title', 'Intake - Our Care')

@section('styles')
    .hero { padding: clamp(58px, 8vw, 104px) var(--pad); background: linear-gradient(100deg, #e6badf 0%, #f6c7d7 52%, #ffd9c7 100%); }
    .hero-grid { display: grid; grid-template-columns: minmax(0,.9fr) minmax(320px,1.1fr); gap: clamp(28px,6vw,72px); align-items: center; }
    .hero h1 { margin: 0 0 16px; color: var(--ink); font-size: clamp(2.35rem, 5vw, 4.25rem); line-height: 1.04; }
    .hero p { max-width: 590px; color: #443454; font-size: 16px; }
    .hero-image, .wide-image { overflow: hidden; min-height: 400px; border-radius: 8px; box-shadow: var(--shadow); }
    .hero-image img, .wide-image img { width: 100%; height: 100%; object-fit: cover; }
    .heading { max-width: 740px; margin: 0 auto 34px; text-align: center; }
    .heading h2 { margin: 0 0 12px; color: var(--ink); font-size: clamp(1.7rem,3vw,2.35rem); }
    .intake-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; }
    .intake-card { padding: 26px; border-radius: 8px; background: #fff; box-shadow: var(--shadow); }
    .intake-card strong { display: inline-flex; margin-bottom: 14px; color: var(--coral); font-size: 12px; text-transform: uppercase; }
    .intake-card h3 { margin: 0 0 10px; color: var(--ink); font-size: 21px; }
    .intake-card p { margin: 0; font-size: 14px; }
    .split { display: grid; grid-template-columns: minmax(300px,.95fr) minmax(0,1.05fr); gap: 44px; align-items: center; }
    .list { display: grid; gap: 12px; margin: 0 0 24px; padding: 0; list-style: none; }
    .list li { padding: 14px 16px; border-radius: 8px; color: var(--ink); background: #fff; box-shadow: 0 10px 22px rgba(45,18,75,.06); font-size: 14px; font-weight: 800; }
    .cta { padding: 62px var(--pad); color: #fff; background: var(--brand); text-align: center; }
    .cta h2 { margin: 0 0 12px; color: #fff; font-size: clamp(1.8rem,3vw,2.6rem); }
    .cta p { max-width: 650px; margin: 0 auto 22px; color: rgba(255,255,255,.82); }
    @media (max-width: 980px) { .hero-grid, .split, .intake-grid { grid-template-columns: 1fr; } .hero-image { min-height: 300px; } }
@endsection

@section('content')
    <section class="hero">
        <div class="container hero-grid">
            <div>
                <p class="eyebrow">Intake</p>
                <h1>Tell us what support needs to feel right.</h1>
                <p>Intake helps us understand goals, routines, safety needs, preferences, and the services that may be the best fit.</p>
                <a class="button" href="{{ url('/client-register') }}">Start client intake</a>
            </div>
            <div class="hero-image"><img src="{{ asset('hero.jpg') }}" alt="Participant intake conversation"></div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="heading"><h2>How intake works</h2><p>A focused process that gives the team the context needed to support you well.</p></div>
            <div class="intake-grid">
                <article class="intake-card"><strong>Step 01</strong><h3>Share your needs</h3><p>Tell us about the participant, routines, goals, supports, and important preferences.</p></article>
                <article class="intake-card"><strong>Step 02</strong><h3>Review options</h3><p>We help identify practical services and the right pathway for support or worker matching.</p></article>
                <article class="intake-card"><strong>Step 03</strong><h3>Plan next steps</h3><p>Move into onboarding, coordination, applications, scheduling, or a consultation with our team.</p></article>
            </div>
        </div>
    </section>

    <section class="section section--soft">
        <div class="container split">
            <div>
                <p class="eyebrow">Helpful details</p>
                <h2>Bring the everyday picture.</h2>
                <ul class="list">
                    <li>Current support goals and NDIS plan information</li>
                    <li>Preferred days, times, location, and access needs</li>
                    <li>Personal care, transport, domestic, or community support priorities</li>
                    <li>Family, carer, or coordinator contact preferences</li>
                </ul>
                <a class="button" href="{{ url('/contact-v2') }}">Ask a question</a>
            </div>
            <div class="wide-image"><img src="{{ asset('contact.jpg') }}" alt="Community support planning"></div>
        </div>
    </section>

    <section class="cta">
        <div class="container">
            <h2>Start with a clear conversation.</h2>
            <p>Our team can help you understand what information matters and what to do next.</p>
            <a class="button button--light" href="{{ url('/client-register') }}">Begin intake</a>
        </div>
    </section>
@endsection
