@extends('layouts.public-v2', [
    'activePage' => 'services',
    'activeService' => $slug,
    'services' => $services,
])

@section('title', $service['label'] . ' - Our Care')

@section('styles')
    .detail-hero { padding: clamp(58px, 8vw, 104px) var(--pad); background: linear-gradient(100deg, #e6badf 0%, #f6c7d7 52%, #ffd9c7 100%); }
    .detail-hero__grid { display: grid; grid-template-columns: minmax(0,.9fr) minmax(320px,1.1fr); gap: clamp(28px,6vw,72px); align-items: center; }
    .detail-hero h1 { margin: 0 0 16px; color: var(--ink); font-size: clamp(2.25rem, 4.6vw, 4rem); line-height: 1.06; }
    .detail-hero p { max-width: 610px; color: #443454; font-size: 16px; }
    .detail-image { overflow: hidden; min-height: 420px; border-radius: 8px; box-shadow: var(--shadow); }
    .detail-image img { width: 100%; height: 100%; object-fit: cover; }
    .detail-layout { display: grid; grid-template-columns: minmax(260px, .36fr) minmax(0, .64fr); gap: 34px; align-items: start; }
    .side-panel { position: sticky; top: 94px; padding: 26px; border-radius: 8px; background: #fff; box-shadow: var(--shadow); }
    .side-panel h2 { margin: 0 0 12px; color: var(--ink); font-size: 22px; }
    .side-panel p { font-size: 13px; }
    .registration { margin: 0 0 18px; padding: 14px 16px; border-radius: 8px; color: var(--ink); background: #fff2f6; font-size: 13px; font-weight: 800; line-height: 1.45; }
    .copy-panel { padding: clamp(26px, 4vw, 46px); border-radius: 8px; background: #fff; box-shadow: var(--shadow); }
    .copy-panel h2 { margin: 30px 0 14px; color: var(--ink); font-size: 25px; line-height: 1.25; }
    .copy-panel p { margin-bottom: 18px; color: #443454; font-size: 16px; line-height: 1.75; }
    .support-list { display: grid; gap: 16px; margin: 0 0 30px; padding: 0; list-style: none; }
    .support-list li { padding: 18px; border-radius: 8px; color: #443454; background: #fff8f2; font-size: 15px; line-height: 1.6; }
    .support-list strong { color: var(--ink); }
    .detail-actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 30px; }
    .related-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; margin-top: 18px; }
    .related-grid a { padding: 16px; border-radius: 8px; color: var(--ink); background: #fff; box-shadow: 0 10px 22px rgba(45,18,75,.07); font-size: 13px; font-weight: 900; }
    @media (max-width: 980px) { .detail-hero__grid, .detail-layout, .related-grid { grid-template-columns: 1fr; } .side-panel { position: static; } .detail-image { min-height: 300px; } }
@endsection

@section('content')
    <section class="detail-hero">
        <div class="container detail-hero__grid">
            <div>
                <p class="eyebrow">{{ $service['label'] }}</p>
                <h1>{{ $service['title'] }}</h1>
                <p>{{ $service['summary'] }}</p>
                <a class="button" href="{{ url('/intake-v2') }}">Book a consultation</a>
            </div>
            <div class="detail-image"><img src="{{ asset($service['image']) }}" alt="{{ $service['label'] }}"></div>
        </div>
    </section>

    <section class="section">
        <div class="container detail-layout">
            <aside class="side-panel">
                <h2>Service pathway</h2>
                <div class="registration">{{ $service['registration'] }}</div>
                <p>Our team can help you understand fit, intake details, and the best next step for this support.</p>
                <a class="button" href="{{ url('/contact-v2') }}">Ask about this service</a>
            </aside>

            <article class="copy-panel">
                @if(!empty($service['heading']))
                    <h2>{{ $service['heading'] }}</h2>
                @endif

                @foreach($service['intro'] as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach

                <h2>{{ $service['section_heading'] }}</h2>
                <p>{{ $service['section_intro'] }}</p>

                @if(!empty($service['items_heading']))
                    <h2>{{ $service['items_heading'] }}</h2>
                @endif

                @if(!empty($service['items_intro']))
                    <p>{{ $service['items_intro'] }}</p>
                @endif

                <ul class="support-list">
                    @foreach($service['items'] as $item)
                        <li><strong>{{ $item['title'] }}:</strong> {{ $item['text'] }}</li>
                    @endforeach
                </ul>

                <div class="detail-actions">
                    <a class="button" href="{{ url('/signup-option') }}">Book a consultation</a>
                    <a class="button button--light" href="{{ url('/services-v2') }}">View all services</a>
                </div>

                <h2>Explore related services</h2>
                <div class="related-grid">
                    @foreach(array_slice($services, 0, 3, true) as $relatedSlug => $relatedService)
                        <a href="{{ url('/services/' . $relatedSlug) }}">{{ $relatedService['label'] }}</a>
                    @endforeach
                </div>
            </article>
        </div>
    </section>
@endsection
