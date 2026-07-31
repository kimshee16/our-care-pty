@extends('layouts.dashboard')

@section('page-title', 'CMS')

@section('content')
@php
    $brand = $cms['brand'] ?? config('cms.brand');
    $palette = $cms['palette'] ?? config('cms.palette');
    $pages = $cms['pages'] ?? config('cms.pages');
    $previewPaths = [
        'home-v2' => 'cms/home',
        'about-v2' => 'cms/about',
        'services-v2' => 'cms/services',
        'onboarding-v2' => 'cms/onboarding',
        'intake-v2' => 'cms/intake',
        'contact-v2' => 'cms/contact',
    ];
@endphp

<div class="dashboard-content cms-admin">
    <div class="dashboard-header">
        <h1>CMS</h1>
        <p>Manage the public Our Care site content, colors, logo, images, and sections.</p>
    </div>

    @if(session('status'))
        <div class="cms-alert cms-alert-success">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="cms-alert cms-alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="cms-grid">
        <section class="cms-panel">
            <div class="cms-panel-header">
                <div>
                    <h2>Brand</h2>
                    <span>Logo and site identity</span>
                </div>
            </div>

            <form method="POST" action="{{ url('/admin/cms/brand') }}" enctype="multipart/form-data" class="cms-form">
                @csrf
                <label>
                    Site Name
                    <input type="text" name="site_name" value="{{ old('site_name', $brand['site_name'] ?? '') }}" required>
                </label>
                <label>
                    Tagline
                    <input type="text" name="tagline" value="{{ old('tagline', $brand['tagline'] ?? '') }}">
                </label>
                <label>
                    Current Logo Path
                    <input type="text" name="logo_path" value="{{ old('logo_path', $brand['logo'] ?? '') }}">
                </label>
                <label>
                    Upload New Logo
                    <input type="file" name="logo_upload" accept="image/*">
                </label>
                @if(!empty($brand['logo']))
                    <img class="cms-preview-logo" src="{{ asset($brand['logo']) }}" alt="Current logo">
                @endif
                <button type="submit" class="cms-button">Save Brand</button>
            </form>
        </section>

        <section class="cms-panel">
            <div class="cms-panel-header">
                <div>
                    <h2>Color Palette</h2>
                    <span>Used by CMS-rendered public pages</span>
                </div>
                <form method="POST" action="{{ url('/admin/cms/reset') }}">
                    @csrf
                    <input type="hidden" name="key" value="palette">
                    <button type="submit" class="cms-link-button">Reset</button>
                </form>
            </div>

            <form method="POST" action="{{ url('/admin/cms/palette') }}" class="cms-form cms-colors">
                @csrf
                @foreach(['primary', 'secondary', 'accent', 'background', 'surface', 'text'] as $color)
                    <label>
                        {{ ucfirst($color) }}
                        <span>
                            <input type="color" name="{{ $color }}" value="{{ old($color, $palette[$color] ?? config("cms.palette.$color")) }}">
                            <input type="text" value="{{ old($color, $palette[$color] ?? config("cms.palette.$color")) }}" data-color-text="{{ $color }}" aria-label="{{ ucfirst($color) }} hex value">
                        </span>
                    </label>
                @endforeach
                <button type="submit" class="cms-button">Save Palette</button>
            </form>
        </section>
    </div>

    <section class="cms-panel">
        <div class="cms-panel-header">
            <div>
                <h2>Pages</h2>
                <span>Edit top-level public pages and their content sections</span>
            </div>
        </div>

        <div class="cms-stack">
            @foreach($pageSlugs as $slug)
                @php
                    $page = $pages[$slug] ?? config("cms.pages.$slug");
                    $sections = $page['sections'] ?? [];
                @endphp
                <details class="cms-editor" id="page-{{ $slug }}" {{ $loop->first ? 'open' : '' }}>
                    <summary>
                        <span>{{ $page['label'] ?? $slug }}</span>
                        <a href="{{ url('/' . ($previewPaths[$slug] ?? $slug)) }}" target="_blank">Preview</a>
                    </summary>
                    <form method="POST" action="{{ url('/admin/cms/pages/' . $slug) }}" enctype="multipart/form-data" class="cms-form">
                        @csrf
                        <div class="cms-two">
                            <label>
                                Navigation Label
                                <input type="text" name="label" value="{{ old('label', $page['label'] ?? '') }}" required>
                            </label>
                            <label>
                                Page Title
                                <input type="text" name="title" value="{{ old('title', $page['title'] ?? '') }}" required>
                            </label>
                        </div>
                        <label>
                            Hero Heading
                            <input type="text" name="hero_title" value="{{ old('hero_title', $page['hero_title'] ?? '') }}" required>
                        </label>
                        <label>
                            Hero Subheading
                            <textarea name="hero_subtitle" rows="3">{{ old('hero_subtitle', $page['hero_subtitle'] ?? '') }}</textarea>
                        </label>
                        <div class="cms-two">
                            <label>
                                Hero Image Path
                                <input type="text" name="hero_image_path" value="{{ old('hero_image_path', $page['hero_image'] ?? '') }}">
                            </label>
                            <label>
                                Upload Hero Image
                                <input type="file" name="hero_image_upload" accept="image/*">
                            </label>
                        </div>
                        <div class="cms-two">
                            <label>
                                Intro Title
                                <input type="text" name="intro_title" value="{{ old('intro_title', $page['intro_title'] ?? '') }}">
                            </label>
                            <label>
                                Intro Text
                                <textarea name="intro_text" rows="4">{{ old('intro_text', $page['intro_text'] ?? '') }}</textarea>
                            </label>
                        </div>
                        <div class="cms-section-list" data-section-list>
                            @forelse($sections as $section)
                                <div class="cms-section-row">
                                    <input type="text" name="section_titles[]" value="{{ $section['title'] ?? '' }}" placeholder="Section title">
                                    <textarea name="section_texts[]" rows="2" placeholder="Section text">{{ $section['text'] ?? '' }}</textarea>
                                    <button type="button" data-remove-row>&times;</button>
                                </div>
                            @empty
                                <div class="cms-section-row">
                                    <input type="text" name="section_titles[]" placeholder="Section title">
                                    <textarea name="section_texts[]" rows="2" placeholder="Section text"></textarea>
                                    <button type="button" data-remove-row>&times;</button>
                                </div>
                            @endforelse
                        </div>
                        <div class="cms-actions">
                            <button type="button" class="cms-secondary-button" data-add-row>Add Section</button>
                            <button type="submit" class="cms-button">Save Page</button>
                        </div>
                    </form>
                </details>
            @endforeach
        </div>
    </section>

    <section class="cms-panel">
        <div class="cms-panel-header">
            <div>
                <h2>Services</h2>
                <span>Edit service pages, summaries, images, and item sections</span>
            </div>
        </div>

        <div class="cms-stack">
            @foreach($services as $slug => $service)
                @php
                    $items = $service['items'] ?? [];
                    $intro = implode("\n\n", $service['intro'] ?? []);
                @endphp
                <details class="cms-editor" id="service-{{ $slug }}">
                    <summary>
                        <span>{{ $service['label'] ?? $slug }}</span>
                        <a href="{{ url('/cms/services/' . $slug) }}" target="_blank">Preview</a>
                    </summary>
                    <form method="POST" action="{{ url('/admin/cms/services/' . $slug) }}" enctype="multipart/form-data" class="cms-form">
                        @csrf
                        <div class="cms-two">
                            <label>
                                Label
                                <input type="text" name="label" value="{{ $service['label'] ?? '' }}" required>
                            </label>
                            <label>
                                Title
                                <input type="text" name="title" value="{{ $service['title'] ?? '' }}" required>
                            </label>
                        </div>
                        <label>
                            Heading
                            <input type="text" name="heading" value="{{ $service['heading'] ?? '' }}">
                        </label>
                        <label>
                            Summary
                            <textarea name="summary" rows="3">{{ $service['summary'] ?? '' }}</textarea>
                        </label>
                        <label>
                            Registration
                            <input type="text" name="registration" value="{{ $service['registration'] ?? '' }}">
                        </label>
                        <div class="cms-two">
                            <label>
                                Image Path
                                <input type="text" name="image_path" value="{{ $service['image'] ?? '' }}">
                            </label>
                            <label>
                                Upload Image
                                <input type="file" name="image_upload" accept="image/*">
                            </label>
                        </div>
                        <label>
                            Intro Paragraphs
                            <textarea name="intro" rows="6">{{ $intro }}</textarea>
                        </label>
                        <div class="cms-two">
                            <label>
                                Section Heading
                                <input type="text" name="section_heading" value="{{ $service['section_heading'] ?? '' }}">
                            </label>
                            <label>
                                Section Intro
                                <textarea name="section_intro" rows="3">{{ $service['section_intro'] ?? '' }}</textarea>
                            </label>
                        </div>
                        <div class="cms-section-list" data-section-list>
                            @forelse($items as $item)
                                <div class="cms-section-row">
                                    <input type="text" name="item_titles[]" value="{{ $item['title'] ?? '' }}" placeholder="Item title">
                                    <textarea name="item_texts[]" rows="2" placeholder="Item text">{{ $item['text'] ?? '' }}</textarea>
                                    <button type="button" data-remove-row>&times;</button>
                                </div>
                            @empty
                                <div class="cms-section-row">
                                    <input type="text" name="item_titles[]" placeholder="Item title">
                                    <textarea name="item_texts[]" rows="2" placeholder="Item text"></textarea>
                                    <button type="button" data-remove-row>&times;</button>
                                </div>
                            @endforelse
                        </div>
                        <div class="cms-actions">
                            <button type="button" class="cms-secondary-button" data-add-row>Add Item</button>
                            <button type="submit" class="cms-button">Save Service</button>
                        </div>
                    </form>
                </details>
            @endforeach
        </div>
    </section>
</div>

<style>
    .cms-admin { display: grid; gap: 24px; }
    .cms-grid { display: grid; grid-template-columns: minmax(280px, 420px) 1fr; gap: 24px; align-items: start; }
    .cms-panel { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow: hidden; }
    .cms-panel-header { display: flex; justify-content: space-between; gap: 16px; align-items: start; padding: 20px 24px; border-bottom: 1px solid #e5e7eb; }
    .cms-panel-header h2 { margin: 0; font-size: 20px; color: #111827; }
    .cms-panel-header span { display: block; margin-top: 4px; color: #6b7280; font-size: 14px; }
    .cms-form { display: grid; gap: 16px; padding: 24px; }
    .cms-form label { display: grid; gap: 8px; color: #111827; font-weight: 700; font-size: 14px; }
    .cms-form input[type="text"], .cms-form textarea, .cms-form input[type="file"] { width: 100%; padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 8px; background: #f8fafc; color: #111827; font: inherit; font-weight: 500; }
    .cms-form textarea { resize: vertical; line-height: 1.5; }
    .cms-colors label span { display: grid; grid-template-columns: 56px 1fr; gap: 10px; }
    .cms-colors input[type="color"] { width: 56px; height: 44px; padding: 2px; border: 1px solid #d1d5db; border-radius: 8px; background: #fff; }
    .cms-button, .cms-secondary-button, .cms-link-button { border: none; border-radius: 8px; padding: 12px 16px; font-weight: 800; cursor: pointer; font: inherit; }
    .cms-button { background: var(--accent); color: #fff; }
    .cms-secondary-button { background: #eef2ff; color: var(--accent); }
    .cms-link-button { background: #f3f4f6; color: #374151; padding: 9px 12px; }
    .cms-two { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; align-items: start; }
    .cms-stack { display: grid; gap: 14px; padding: 18px; }
    .cms-editor { border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; background: #fff; }
    .cms-editor summary { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 16px 18px; background: #f9fafb; cursor: pointer; color: #111827; font-weight: 800; }
    .cms-editor summary a { color: var(--accent); font-size: 13px; text-decoration: none; }
    .cms-section-list { display: grid; gap: 12px; }
    .cms-section-row { display: grid; grid-template-columns: minmax(160px, 260px) 1fr 40px; gap: 10px; align-items: stretch; }
    .cms-section-row button { border: none; border-radius: 8px; background: #fee2e2; color: #b91c1c; font-size: 22px; cursor: pointer; }
    .cms-actions { display: flex; justify-content: flex-end; gap: 12px; flex-wrap: wrap; }
    .cms-alert { padding: 16px; border-radius: 8px; font-weight: 700; }
    .cms-alert ul { margin: 0; padding-left: 20px; }
    .cms-alert-success { background: #ecfdf5; color: #047857; border: 1px solid #bbf7d0; }
    .cms-alert-danger { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
    .cms-preview-logo { width: 120px; max-height: 120px; object-fit: contain; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px; }
    @media (max-width: 1000px) { .cms-grid, .cms-two, .cms-section-row { grid-template-columns: 1fr; } .cms-section-row button { height: 40px; } }
</style>

<script>
    document.addEventListener('input', function(event) {
        if (event.target.matches('input[type="color"]')) {
            const text = document.querySelector('[data-color-text="' + event.target.name + '"]');
            if (text) text.value = event.target.value;
        }
    });

    document.addEventListener('click', function(event) {
        if (event.target.matches('[data-add-row]')) {
            const form = event.target.closest('form');
            const list = form.querySelector('[data-section-list]');
            const first = list.querySelector('.cms-section-row');
            const clone = first.cloneNode(true);
            clone.querySelectorAll('input, textarea').forEach(function(input) { input.value = ''; });
            list.appendChild(clone);
        }

        if (event.target.matches('[data-remove-row]')) {
            const row = event.target.closest('.cms-section-row');
            const list = event.target.closest('[data-section-list]');
            if (list.querySelectorAll('.cms-section-row').length > 1) {
                row.remove();
            } else {
                row.querySelectorAll('input, textarea').forEach(function(input) { input.value = ''; });
            }
        }
    });
</script>
@endsection
