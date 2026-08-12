@extends('layouts.public-v2', ['activePage' => 'contact'])

@section('title', 'Contact Our Care')

@section('styles')
    .hero { padding: clamp(58px, 8vw, 104px) var(--pad); background: linear-gradient(100deg, #e6badf 0%, #f6c7d7 52%, #ffd9c7 100%); }
    .hero-grid { display: grid; grid-template-columns: minmax(0,.9fr) minmax(320px,1.1fr); gap: clamp(28px,6vw,72px); align-items: center; }
    .hero h1 { margin: 0 0 16px; color: var(--ink); font-size: clamp(2.35rem, 5vw, 4.25rem); line-height: 1.04; }
    .hero p { max-width: 590px; color: #443454; font-size: 16px; }
    .hero-image { overflow: hidden; min-height: 400px; border-radius: 8px; box-shadow: var(--shadow); }
    .hero-image img { width: 100%; height: 100%; object-fit: cover; }
    .contact-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; }
    .contact-card, .office-card { padding: 28px; border-radius: 8px; background: #fff; box-shadow: var(--shadow); }
    .contact-card strong { display: block; margin-bottom: 12px; color: var(--coral); font-size: 12px; text-transform: uppercase; }
    .contact-card h3, .office-card h3 { margin: 0 0 10px; color: var(--ink); font-size: 22px; }
    .contact-card p, .office-card p { margin: 0 0 12px; font-size: 14px; }
    .contact-card a { color: var(--coral); font-weight: 900; }
    .office-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 24px; }
    .heading { max-width: 740px; margin: 0 auto 34px; text-align: center; }
    .heading h2 { margin: 0 0 12px; color: var(--ink); font-size: clamp(1.7rem,3vw,2.35rem); }
    .form-panel { display: grid; grid-template-columns: minmax(0, .95fr) minmax(280px, 1.05fr); gap: 34px; align-items: start; }
    .form-note { padding: 30px; border-radius: 8px; color: #fff; background: var(--brand); }
    .form-note h2 { margin: 0 0 12px; color: #fff; font-size: clamp(1.8rem,3vw,2.6rem); }
    .form-note p { color: rgba(255,255,255,.82); }
    .field-stack { display: grid; gap: 14px; padding: 30px; border-radius: 8px; background: #fff; box-shadow: var(--shadow); }
    .field { display: grid; gap: 6px; }
    .field label { color: var(--ink); font-size: 12px; font-weight: 900; }
    .field input, .field textarea { width: 100%; border: 1px solid var(--line); border-radius: 8px; padding: 12px 14px; color: var(--ink); font: inherit; }
    .field textarea { min-height: 130px; resize: vertical; }
    @media (max-width: 980px) { .hero-grid, .contact-grid, .office-grid, .form-panel { grid-template-columns: 1fr; } .hero-image { min-height: 300px; } }
@endsection

@section('content')
    <section class="hero">
        <div class="container hero-grid">
            <div>
                <p class="eyebrow">Contact</p>
                <h1>Talk with Our Care about the next step.</h1>
                <p>Reach out for intake questions, service guidance, worker onboarding, or general support information.</p>
                <a class="button" href="tel:0425795830">Call 0425 795 830</a>
            </div>
            <div class="hero-image"><img src="{{ asset('contact.jpg') }}" alt="Our Care community support"></div>
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
                <article class="office-card"><h3>Sydney head office</h3><p>Care coordination and participant enquiries.</p><p><a class="button" href="mailto:admin@ourcarepty.com">Email Sydney</a></p></article>
                <article class="office-card"><h3>Melbourne support desk</h3><p>Worker onboarding and service information.</p><p><a class="button" href="mailto:admin@ourcarepty.com">Email Melbourne</a></p></article>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container form-panel">
            <div class="form-note">
                <h2>What should I include?</h2>
                <p>Tell us whether you are a participant, family member, coordinator, or worker. Include the type of support you need, your location, and the best way to reach you.</p>
            </div>
            <form class="field-stack" action="mailto:admin@ourcarepty.com" method="get">
                <div class="field"><label for="name">Name</label><input id="name" name="subject" type="text" placeholder="Your name"></div>
                <div class="field"><label for="email">Email</label><input id="email" name="cc" type="email" placeholder="you@example.com"></div>
                <div class="field"><label for="message">Message</label><textarea id="message" name="body" placeholder="How can we help?"></textarea></div>
                <button class="button" type="submit">Send message</button>
            </form>
        </div>
    </section>
@endsection
