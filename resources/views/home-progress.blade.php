@extends('layouts.public-v2', ['activePage' => 'home'])

@section('title', 'Our Care - Home')

@section('styles')
    :root {
        --home-plum: #2d124b;
        --home-orange: #ff7044;
        --home-ink: #2c1746;
        --home-muted: #6f6278;
    }

    body { background: #fffaf7; }
    .topbar { min-height: 28px; padding-top: 5px; padding-bottom: 5px; border-bottom: 0; color: rgba(255,255,255,.86); background: var(--home-plum); font-size: 11px; }
    .topbar a { color: #fff; font-weight: 800; }
    .topbar .icon { width: 14px; height: 14px; }
    .site-header { min-height: 52px; padding-top: 8px; padding-bottom: 8px; border-bottom: 1px solid rgba(45,18,75,.08); background: rgba(255,255,255,.96); box-shadow: 0 10px 28px rgba(45,18,75,.06); }
    .brand-link img { width: 44px; height: 44px; }
    .brand-wordmark strong { color: var(--home-plum); font-size: 16px; }
    .brand-wordmark span { color: var(--home-orange); font-size: 10px; font-weight: 900; text-transform: uppercase; }
    .nav-links { gap: 18px; color: var(--home-ink); font-size: 12px; }
    .nav-links a:hover, .nav-links a[aria-current="page"], .nav-trigger:hover, .nav-item:hover .nav-trigger { color: var(--home-orange); }
    .footer { display: none; }

    .button, .home-btn { display: inline-flex; align-items: center; justify-content: center; min-height: 40px; padding: 11px 18px; border: 0; border-radius: 999px; color: #fff; background: var(--home-orange); box-shadow: 0 12px 24px rgba(255,112,68,.26); font-size: 12px; font-weight: 900; line-height: 1; }
    .button:hover, .home-btn:hover { transform: translateY(-1px); box-shadow: 0 16px 30px rgba(255,112,68,.32); }

    .home-wrap { width: min(100%, 1080px); margin: 0 auto; }
    .home-section { padding: clamp(54px, 7vw, 92px) var(--pad); background: #fff; }
    .home-section.pink { background: linear-gradient(180deg, #ffd3df 0%, #ffd6c3 100%); }
    .home-section.warm { background: linear-gradient(180deg, #ffdcc7 0%, #fff3d0 100%); }
    .home-section.soft { background: #fff8f2; }
    .home-heading { max-width: 760px; margin: 0 auto 34px; text-align: center; }
    .home-heading h2 { margin: 0 0 12px; color: var(--home-ink); font-size: clamp(1.7rem, 3.2vw, 2.35rem); line-height: 1.15; }
    .home-heading p { margin: 0; color: var(--home-muted); font-size: 14px; }

    .home-hero { overflow: hidden; min-height: min(620px, calc(100vh - 80px)); padding: clamp(58px, 8vw, 104px) var(--pad) 0; background: linear-gradient(100deg, #e6badf 0%, #f5c4d0 50%, #f9d0ca 100%); }
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
    .fact-list li { padding: 15px 16px; border-radius: 8px; color: var(--home-ink); background: #fff8f2; box-shadow: 0 10px 22px rgba(45,18,75,.06); font-size: 14px; font-weight: 800; }

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
        $services = config('ourcare_v2.services');
        $serviceCards = array_slice($services, 0, 8, true);
        $trust = [
            ['icon' => '01', 'title' => 'Experienced Guidance', 'text' => 'Support through NDIS options, intake, and planning.'],
            ['icon' => '02', 'title' => 'Personalised Support', 'text' => 'Care shaped around routines, preferences, and goals.'],
            ['icon' => '03', 'title' => 'Up-to-Date Information', 'text' => 'Planning that keeps pace with service changes.'],
            ['icon' => '04', 'title' => 'Trusted and Transparent', 'text' => 'Clear communication before, during, and after support.'],
            ['icon' => '05', 'title' => 'Career-Focused Pathways', 'text' => 'Profile and onboarding support for care workers.'],
            ['icon' => '06', 'title' => 'End-to-End Service', 'text' => 'From first enquiry through matching and support.'],
        ];
    @endphp

    <section class="home-hero">
        <div class="home-hero-grid">
            <div class="home-hero-copy">
                <h1>The caring choice for NDIS support in Australia</h1>
                <p>Plan your support, connect with trusted workers, and build routines that protect dignity, independence, and everyday wellbeing.</p>
                <a class="home-btn" href="{{ url('/intake-v2') }}">Book a consultation</a>
                <small class="home-note">Local, person-centred support for participants, families, carers, and community support workers.</small>
            </div>
            <div class="home-hero-media"><img src="{{ asset('hero.jpg') }}" alt="Our Care support worker assisting a participant"></div>
        </div>
    </section>

    <section class="home-section">
        <div class="home-wrap">
            <div class="home-heading"><h2>NDIS and community support services in Australia</h2></div>
            <div class="intro-grid">
                <div class="intro-copy">
                    <p>Our Care helps people find reliable support for daily living, personal care, community participation, transport, counselling, respite, and capacity building.</p>
                    <p>We keep the process clear from first enquiry through intake, worker matching, onboarding, and ongoing service coordination.</p>
                </div>
                <ul class="fact-list">
                    <li>NDIS-aligned intake, planning, and service guidance</li>
                    <li>Qualified workers for home, community, and daily routines</li>
                    <li>Support coordination across goals, providers, and budgets</li>
                </ul>
            </div>
            <div class="pathway-grid">
                <a class="image-card" href="{{ url('/services-v2') }}"><img src="{{ asset('contact.jpg') }}" alt="Participant and support worker outdoors"><span class="image-card-body"><h3>NDIS Services</h3><p>Explore practical supports for everyday life.</p></span></a>
                <a class="image-card" href="{{ url('/healthcare-register') }}"><img src="{{ asset('ready.jpg') }}" alt="Support worker preparing care"><span class="image-card-body"><h3>Join Our Team</h3><p>Apply to become an Our Care worker.</p></span></a>
                <a class="image-card" href="{{ url('/client-register') }}"><img src="{{ asset('hero.jpg') }}" alt="Client speaking with a support worker"><span class="image-card-body"><h3>Start Intake</h3><p>Tell us what support you need.</p></span></a>
            </div>
        </div>
    </section>

    <section class="home-section pink">
        <div class="home-wrap">
            <div class="home-heading"><h2>Why clients trust Our Care</h2><p>We are committed to support that feels clear, respectful, and dependable from the first conversation.</p></div>
            <div class="trust-grid">
                @foreach($trust as $item)
                    <article class="trust-card"><span class="trust-icon">{{ $item['icon'] }}</span><h3>{{ $item['title'] }}</h3><p>{{ $item['text'] }}</p></article>
                @endforeach
            </div>
            <div class="rating-card"><div class="stars" aria-label="Five star rating">*****</div><p>Our Care made support easier to understand and easier to start. The team listened and helped us feel ready.</p></div>
            <div class="home-heading" style="margin-bottom: 0;"><a class="home-btn" href="{{ url('/intake-v2') }}">Start your journey today</a></div>
        </div>
    </section>

    <section class="home-section">
        <div class="home-wrap">
            <div class="home-heading"><h2>Find your support service</h2></div>
            <div class="service-grid">
                @foreach($serviceCards as $slug => $service)
                    <a class="service-tile" href="{{ route('services.detail.v2', $slug) }}"><span class="service-tile-image"><img src="{{ asset($service['image']) }}" alt="{{ $service['label'] }}"></span><span>{{ $service['label'] }}</span></a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="home-section warm">
        <div class="home-wrap">
            <div class="home-heading"><h2>Join us at an upcoming session</h2></div>
            <div class="event-grid">
                <article class="event-card"><div class="event-poster"><img src="{{ asset('care-vector-04.png') }}" alt="Community care illustration"><div class="event-poster-copy"><small>Our Care</small><strong>Stay tuned for community events</strong></div></div><h3>Stay tuned for upcoming information sessions</h3><p>Register now</p><a class="home-btn" href="{{ url('/contact-v2') }}">Register now</a></article>
                <article class="event-card"><div class="event-poster"><img src="{{ asset('contact.jpg') }}" alt="Support worker and participant outdoors"><div class="event-poster-copy"><small>Info Session</small><strong>Plan your NDIS support with confidence</strong></div></div><h3>Online session: understanding services and intake</h3><p>Tuesday, 7:00 PM</p><a class="home-btn" href="{{ url('/intake-v2') }}">Register now</a></article>
                <article class="event-card"><div class="event-poster"><img src="{{ asset('ready.jpg') }}" alt="Care worker preparing support"><div class="event-poster-copy"><small>Worker Pathway</small><strong>Study, work, and grow in care</strong></div></div><h3>Worker session: profile, onboarding, and opportunities</h3><p>Saturday, 10:00 AM</p><a class="home-btn" href="{{ url('/healthcare-register') }}">Register now</a></article>
            </div>
            <div class="home-heading" style="margin-top: 64px;"><h2>Testimonials</h2><p>People choose Our Care because the process feels human, steady, and clear.</p></div>
            <div class="testimonial-grid">
                <article class="testimonial-card"><div class="stars">*****</div><p>Our support worker is kind, reliable, and respectful of our routine.</p><strong>Family carer</strong></article>
                <article class="testimonial-card"><div class="stars">*****</div><p>The intake team explained everything in plain language and helped us choose the right services.</p><strong>Participant</strong></article>
                <article class="testimonial-card"><div class="stars">*****</div><p>I felt supported through registration and onboarding. The worker pathway was clear from the start.</p><strong>Support worker</strong></article>
            </div>
        </div>
    </section>

    <section class="home-section">
        <div class="home-wrap">
            <div class="home-heading"><h2>Learn more about NDIS support pathways</h2></div>
            <div class="requirement-grid">
                <article class="requirement-card"><h3>Participant Support</h3><p>Receive support shaped around daily wellbeing, independence, and personal goals.</p><ul class="check-list"><li>Choose the right service</li><li>Prepare intake information</li><li>Understand your support plan</li><li>Plan worker matching</li></ul><a class="home-btn" href="{{ url('/client-register') }}">Start intake</a></article>
                <article class="requirement-card"><h3>Worker Pathways</h3><p>Join Our Care as a qualified worker and connect with people who need dependable support.</p><ul class="check-list"><li>Build your worker profile</li><li>Upload qualifications</li><li>Complete onboarding</li><li>Apply for care opportunities</li></ul><a class="home-btn" href="{{ url('/healthcare-register') }}">Become a worker</a></article>
                <article class="requirement-card"><h3>Family Guidance</h3><p>Receive clear help for care decisions, provider coordination, and service planning.</p><ul class="check-list"><li>Plan practical supports</li><li>Coordinate providers</li><li>Review participant goals</li><li>Keep communication clear</li></ul><a class="home-btn" href="{{ url('/contact-v2') }}">Talk to our team</a></article>
            </div>
        </div>
    </section>

    <section class="home-section soft" id="ndis">
        <div class="home-wrap">
            <div class="home-heading"><h2>Stay informed: NDIS updates</h2></div>
            <div class="update-grid">
                <article class="update-card"><img src="{{ asset('ready.jpg') }}" alt="Care worker preparing documents"><div class="update-card-body"><h3>NDIS pricing and support categories</h3><p>Keep care planning current as pricing arrangements and support categories change.</p><a class="text-link" href="{{ url('/services-v2#ndis') }}">Read more</a></div></article>
                <article class="update-card"><img src="{{ asset('care-vector-06.png') }}" alt="Support worker sharing tea with participant"><div class="update-card-body"><h3>Preparing for intake with Our Care</h3><p>A little preparation helps the team understand goals, routines, and support preferences.</p><a class="text-link" href="{{ url('/intake-v2') }}">Read more</a></div></article>
                <article class="update-card"><img src="{{ asset('contact.jpg') }}" alt="Participant supported in the community"><div class="update-card-body"><h3>Choosing services for everyday living</h3><p>Explore personal, domestic, transport, and community supports available.</p><a class="text-link" href="{{ url('/services-v2') }}">Read more</a></div></article>
            </div>
        </div>
    </section>

    <section class="cta-band"><img src="{{ asset('contact.jpg') }}" alt="Our Care participants and support workers outdoors"><div class="cta-content"><h2>Get started today</h2><p>Our professional and helpful team is ready to guide the next step for your support needs.</p><a class="home-btn" href="{{ url('/intake-v2') }}">Book a free consultation</a></div></section>

    <section class="home-section">
        <div class="home-wrap">
            <div class="home-heading"><h2>Find your local office</h2><p>We are here to support you online, by phone, and in person.</p><div class="location-pills"><span>Sydney</span><span>Melbourne</span><span>Brisbane</span><span>Adelaide</span></div></div>
            <div class="office-grid">
                <article class="office-card"><h3>Sydney head office</h3><p>Care coordination and participant enquiries</p><p><a class="text-link" href="mailto:admin@ourcarepty.com">admin@ourcarepty.com</a></p><p><a class="text-link" href="tel:0425795830">0425 795 830</a></p></article>
                <article class="office-card"><h3>Melbourne support desk</h3><p>Worker onboarding and service information</p><p><a class="text-link" href="mailto:admin@ourcarepty.com">admin@ourcarepty.com</a></p><p><a class="text-link" href="tel:0425795830">0425 795 830</a></p></article>
            </div>
        </div>
    </section>

    <section class="home-footer">
        <div class="home-footer-grid">
            <div><img src="{{ asset('logo3.png') }}" alt="Our Care logo"><p>Person-centred NDIS support for participants, families, and support workers.</p></div>
            <div><h3>Services</h3><a href="{{ url('/services-v2') }}">All Services</a><a href="{{ url('/services/personal-care-support') }}">Personal Care</a><a href="{{ url('/services/community-participation') }}">Community Participation</a><a href="{{ url('/services/support-coordination') }}">Support Coordination</a></div>
            <div><h3>Quick Links</h3><a href="{{ url('/about-v2') }}">About Us</a><a href="{{ url('/intake-v2') }}">Intake</a><a href="{{ url('/onboarding-v2') }}">Onboarding</a><a href="{{ url('/contact-v2') }}">Contact Us</a></div>
            <div><h3>Contact</h3><a href="tel:0425795830">0425 795 830</a><a href="mailto:admin@ourcarepty.com">admin@ourcarepty.com</a><p>Sydney, Melbourne, Brisbane, Adelaide</p></div>
        </div>
        <div class="home-footer-bottom"><span>Copyright &copy; {{ date('Y') }} Our Care Pty Ltd.</span><span>Website and design by Our Care</span></div>
    </section>

    <a class="chat-button" href="{{ url('/contact-v2') }}" aria-label="Contact Our Care">?</a>
@endsection
