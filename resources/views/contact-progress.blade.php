@extends('layouts.public-v2', ['activePage' => 'contact'])

@section('title', 'Contact Our Care')

@section('styles')
    .progress-hero { padding: clamp(58px, 8vw, 104px) var(--pad); background: linear-gradient(100deg, #e6badf 0%, #f6c7d7 52%, #ffd9c7 100%); }
    .progress-hero__grid { display: grid; grid-template-columns: minmax(0, .9fr) minmax(320px, 1.1fr); gap: clamp(28px, 6vw, 72px); align-items: center; }
    .progress-hero h1 { margin: 0 0 16px; color: var(--ink); font-size: clamp(2.35rem, 5vw, 4.25rem); line-height: 1.04; }
    .progress-hero p { max-width: 590px; color: #443454; font-size: 16px; }
    .progress-image { overflow: hidden; min-height: 400px; border-radius: 8px; box-shadow: var(--shadow); }
    .progress-image img { width: 100%; height: 100%; object-fit: cover; }
    .contact-grid, .office-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; }
    .office-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .contact-card, .office-card { padding: 28px; border-radius: 8px; background: #fff; box-shadow: var(--shadow); }
    .contact-card strong { display: block; margin-bottom: 12px; color: var(--coral); font-size: 12px; text-transform: uppercase; }
    .contact-card h3, .office-card h3 { margin: 0 0 10px; color: var(--ink); font-size: 22px; }
    .contact-card a { color: var(--coral); font-weight: 900; }
    .heading { max-width: 740px; margin: 0 auto 34px; text-align: center; }
    .heading h2 { margin: 0 0 12px; color: var(--ink); font-size: clamp(1.8rem, 3vw, 2.6rem); }
    @media (max-width: 980px) { .progress-hero__grid, .contact-grid, .office-grid { grid-template-columns: 1fr; } }
@endsection

@section('content')
    <section class="progress-hero">
        <div class="container progress-hero__grid">
            <div><p class="eyebrow">Contact</p><h1>Talk with Our Care about the next step.</h1><p>Reach out for intake questions, service guidance, worker onboarding, or general support information.</p><a class="button" href="tel:0425795830">Call 0425 795 830</a></div>
            <div class="progress-image"><img src="{{ asset('contact.jpg') }}" alt="Our Care community support"></div>
        </div>
    </section>
    <section class="section">
        <div class="container contact-grid">
            <article class="contact-card"><strong>Phone</strong><h3>Call us</h3><p>Speak with the team about intake, services, or onboarding.</p><a href="tel:0425795830">0425 795 830</a></article>
            <article class="contact-card"><strong>Email</strong><h3>Send details</h3><p>Share your question and the best way for us to respond.</p><a href="mailto:admin@ourcarepty.com">admin@ourcarepty.com</a></article>
            <article class="contact-card"><strong>Online</strong><h3>Start intake</h3><p>Begin with a participant or worker pathway online.</p><a href="{{ url('/signup-option') }}">Create account</a></article>
        </div>
    </section>
    <section class="section section--soft">
        <div class="container">
            <div class="heading"><h2>Find your local office</h2><p>We support people online, by phone, and across key Australian locations.</p></div>
            <div class="office-grid">
                <article class="office-card"><h3>Sydney head office</h3><p>Care coordination and participant enquiries.</p><a class="button" href="mailto:admin@ourcarepty.com">Email Sydney</a></article>
                <article class="office-card"><h3>Melbourne support desk</h3><p>Worker onboarding and service information.</p><a class="button" href="mailto:admin@ourcarepty.com">Email Melbourne</a></article>
            </div>
        </div>
    </section>
@endsection
