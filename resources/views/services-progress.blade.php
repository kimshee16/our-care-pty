@extends('layouts.public-v2', ['activePage' => 'services'])

@section('title', 'Our Care Services')

@section('styles')
    .progress-hero { padding: clamp(58px, 8vw, 104px) var(--pad); background: linear-gradient(100deg, #e6badf 0%, #f6c7d7 52%, #ffd9c7 100%); }
    .progress-hero__grid, .ndis-band { display: grid; grid-template-columns: minmax(0, .9fr) minmax(320px, 1.1fr); gap: clamp(28px, 6vw, 72px); align-items: center; }
    .progress-hero h1 { margin: 0 0 16px; color: var(--ink); font-size: clamp(2.4rem, 5vw, 4.4rem); line-height: 1.04; }
    .progress-hero p { max-width: 590px; color: #443454; font-size: 16px; }
    .progress-image { overflow: hidden; min-height: 400px; border-radius: 8px; box-shadow: var(--shadow); }
    .progress-image img, .service-card img { width: 100%; height: 100%; object-fit: cover; }
    .service-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; }
    .service-card { overflow: hidden; border-radius: 8px; background: #fff; box-shadow: var(--shadow); }
    .service-card__image { height: 190px; }
    .service-card__body { padding: 24px; }
    .service-card h3 { margin: 0 0 10px; color: var(--ink); font-size: 21px; }
    .service-card p { min-height: 76px; margin: 0 0 18px; font-size: 14px; }
    .ndis-band h2 { margin: 0 0 16px; color: var(--ink); font-size: clamp(1.9rem, 3vw, 2.6rem); }
    @media (max-width: 980px) { .progress-hero__grid, .ndis-band, .service-grid { grid-template-columns: 1fr; } }
@endsection

@section('content')
    @php($services = config('ourcare_v2.services'))
    <section class="progress-hero">
        <div class="container progress-hero__grid">
            <div>
                <p class="eyebrow">Our services</p>
                <h1>NDIS support for daily life, community, and confidence.</h1>
                <p>Choose practical services delivered with clear communication, respectful workers, and planning that keeps your goals in view.</p>
                <a class="button" href="{{ url('/intake-v2') }}">Book a consultation</a>
            </div>
            <div class="progress-image"><img src="{{ asset('hero.jpg') }}" alt="Support worker assisting a participant"></div>
        </div>
    </section>
    <section class="section">
        <div class="container service-grid">
            @foreach($services as $slug => $service)
                <article class="service-card">
                    <div class="service-card__image"><img src="{{ asset($service['image']) }}" alt="{{ $service['label'] }}"></div>
                    <div class="service-card__body"><h3>{{ $service['label'] }}</h3><p>{{ $service['summary'] }}</p><a class="button" href="{{ url('/services/' . $slug) }}">Learn more</a></div>
                </article>
            @endforeach
        </div>
    </section>
    <section class="section section--soft" id="ndis">
        <div class="container ndis-band">
            <div><p class="eyebrow">NDIS updates</p><h2>Keep support planning current.</h2><p>NDIS pricing, categories, and plan usage can change. Our Care helps participants and families review service needs, prepare intake information, and keep support practical.</p><a class="button" href="{{ url('/contact-v2') }}">Ask about NDIS support</a></div>
            <div class="progress-image"><img src="{{ asset('ready.jpg') }}" alt="Care worker preparing support information"></div>
        </div>
    </section>
@endsection
