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

    <div class="cms-builder-shell">
        <aside class="cms-builder-sidebar" aria-label="CMS builder navigation">
            <div class="cms-builder-brand">
                @if(!empty($brand['logo']))
                    <img src="{{ asset($brand['logo']) }}" alt="Current logo">
                @endif
                <div>
                    <strong>{{ $brand['site_name'] ?? 'Our Care' }}</strong>
                    <span>Visual CMS Builder</span>
                </div>
            </div>
            <nav>
                <a href="#site-kit">Site Kit</a>
                <a href="#page-builder">Pages</a>
                <a href="#homepage-builder">Homepage</a>
                <a href="#service-library">Services</a>
            </nav>
            <a class="cms-sidebar-preview" href="{{ url('/cms/home') }}" target="_blank">Open Homepage Preview</a>
        </aside>

        <main class="cms-builder-main">
            <section id="site-kit" class="cms-builder-stage">
                <div class="cms-stage-title">
                    <div>
                        <h2>Site Kit</h2>
                        <p>Brand and palette settings used by CMS-rendered public pages.</p>
                    </div>
                    <a href="{{ url('/cms/home') }}" target="_blank">Preview site</a>
                </div>

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
                <div class="cms-two">
                    <label>
                        Public Phone
                        <input type="text" name="phone" value="{{ old('phone', $brand['phone'] ?? '') }}">
                    </label>
                    <label>
                        Public Email
                        <input type="text" name="email" value="{{ old('email', $brand['email'] ?? '') }}">
                    </label>
                </div>
                <div class="cms-two">
                    <label>
                        Sign In Label
                        <input type="text" name="sign_in_label" value="{{ old('sign_in_label', $brand['sign_in_label'] ?? '') }}">
                    </label>
                    <label>
                        Create Account Label
                        <input type="text" name="create_account_label" value="{{ old('create_account_label', $brand['create_account_label'] ?? '') }}">
                    </label>
                </div>
                <div class="cms-two">
                    <label>
                        Updates Nav Label
                        <input type="text" name="updates_label" value="{{ old('updates_label', $brand['updates_label'] ?? '') }}">
                    </label>
                    <label>
                        Updates Nav URL
                        <input type="text" name="updates_url" value="{{ old('updates_url', $brand['updates_url'] ?? '') }}">
                    </label>
                </div>
                <div class="cms-two">
                    <label>
                        Footer Services Heading
                        <input type="text" name="footer_services_label" value="{{ old('footer_services_label', $brand['footer_services_label'] ?? '') }}">
                    </label>
                    <label>
                        Footer Quick Links Heading
                        <input type="text" name="footer_quick_links_label" value="{{ old('footer_quick_links_label', $brand['footer_quick_links_label'] ?? '') }}">
                    </label>
                </div>
                <label>
                    Footer Contact Heading
                    <input type="text" name="footer_contact_label" value="{{ old('footer_contact_label', $brand['footer_contact_label'] ?? '') }}">
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
            </section>

    <section class="cms-panel cms-builder-stage" id="page-builder">
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
                        <span><small>Page</small>{{ $page['label'] ?? $slug }}</span>
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
                        @if($slug === 'home-v2')
                            @php
                                $facts = $page['intro_facts'] ?? [];
                                $pathways = $page['pathways'] ?? [];
                                $trustItems = $page['trust_items'] ?? [];
                                $events = $page['events'] ?? [];
                                $testimonials = $page['testimonials'] ?? [];
                                $requirements = $page['requirements'] ?? [];
                                $updates = $page['updates'] ?? [];
                                $locations = implode(', ', $page['locations'] ?? []);
                                $offices = $page['offices'] ?? [];
                            @endphp
                            <div class="cms-home-builder" id="homepage-builder">
                                <div class="cms-builder-top">
                                    <div>
                                        <span>Homepage Canvas</span>
                                        <h3>Section inspector</h3>
                                    </div>
                                    <a href="{{ url('/cms/home') }}" target="_blank">Preview</a>
                                </div>

                                <div class="cms-home-layout">
                                    <aside class="cms-section-navigator" aria-label="Homepage section navigation">
                                        <a href="#block-hero">Hero</a>
                                        <a href="#block-intro">Intro</a>
                                        <a href="#block-pathways">Pathways</a>
                                        <a href="#block-trust">Trust</a>
                                        <a href="#block-events">Events</a>
                                        <a href="#block-testimonials">Testimonials</a>
                                        <a href="#block-requirements">Pathways Info</a>
                                        <a href="#block-updates">Updates</a>
                                        <a href="#block-cta">CTA and Footer</a>
                                    </aside>
                                    <div class="cms-inspector-stack">

                                <div class="cms-home-group" id="block-hero">
                                    <h4>Hero</h4>
                                    <div class="cms-two">
                                    <label>
                                        Hero CTA Label
                                        <input type="text" name="hero_cta_label" value="{{ $page['hero_cta_label'] ?? '' }}">
                                    </label>
                                    <label>
                                        Hero CTA URL
                                        <input type="text" name="hero_cta_url" value="{{ $page['hero_cta_url'] ?? '' }}">
                                    </label>
                                    </div>
                                    <label>
                                        Hero Note
                                        <textarea name="hero_note" rows="2">{{ $page['hero_note'] ?? '' }}</textarea>
                                    </label>
                                </div>

                                <div class="cms-home-group" id="block-intro">
                                    <h4>Intro Facts</h4>
                                    <div class="cms-section-list" data-section-list>
                                        @forelse($facts as $fact)
                                            <div class="cms-section-row cms-one-input-row">
                                                <input type="text" name="intro_facts[]" value="{{ $fact }}" placeholder="Fact">
                                                <button type="button" data-remove-row>&times;</button>
                                            </div>
                                        @empty
                                            <div class="cms-section-row cms-one-input-row">
                                                <input type="text" name="intro_facts[]" placeholder="Fact">
                                                <button type="button" data-remove-row>&times;</button>
                                            </div>
                                        @endforelse
                                    </div>
                                    <button type="button" class="cms-secondary-button" data-add-row>Add Fact</button>
                                </div>

                                <div class="cms-home-group" id="block-pathways">
                                    <h4>Pathway Cards</h4>
                                    <div class="cms-section-list" data-section-list>
                                        @forelse($pathways as $pathway)
                                            <div class="cms-section-row cms-wide-row">
                                                <input type="text" name="pathway_titles[]" value="{{ $pathway['title'] ?? '' }}" placeholder="Title">
                                                <textarea name="pathway_texts[]" rows="2" placeholder="Text">{{ $pathway['text'] ?? '' }}</textarea>
                                                <input type="text" name="pathway_images[]" value="{{ $pathway['image'] ?? '' }}" placeholder="Image path">
                                                <input type="text" name="pathway_urls[]" value="{{ $pathway['url'] ?? '' }}" placeholder="URL">
                                                <button type="button" data-remove-row>&times;</button>
                                            </div>
                                        @empty
                                            <div class="cms-section-row cms-wide-row">
                                                <input type="text" name="pathway_titles[]" placeholder="Title">
                                                <textarea name="pathway_texts[]" rows="2" placeholder="Text"></textarea>
                                                <input type="text" name="pathway_images[]" placeholder="Image path">
                                                <input type="text" name="pathway_urls[]" placeholder="URL">
                                                <button type="button" data-remove-row>&times;</button>
                                            </div>
                                        @endforelse
                                    </div>
                                    <button type="button" class="cms-secondary-button" data-add-row>Add Pathway</button>
                                </div>

                                <div class="cms-home-group" id="block-trust">
                                    <h4>Trust Section</h4>
                                    <div class="cms-two">
                                        <label>
                                            Trust Heading
                                            <input type="text" name="trust_heading" value="{{ $page['trust_heading'] ?? '' }}">
                                        </label>
                                        <label>
                                            Trust Text
                                            <textarea name="trust_text" rows="2">{{ $page['trust_text'] ?? '' }}</textarea>
                                        </label>
                                    </div>
                                    <div class="cms-section-list" data-section-list>
                                        @forelse($trustItems as $item)
                                            <div class="cms-section-row cms-wide-row">
                                                <input type="text" name="trust_icons[]" value="{{ $item['icon'] ?? '' }}" placeholder="Icon/number">
                                                <input type="text" name="trust_titles[]" value="{{ $item['title'] ?? '' }}" placeholder="Title">
                                                <textarea name="trust_texts[]" rows="2" placeholder="Text">{{ $item['text'] ?? '' }}</textarea>
                                                <button type="button" data-remove-row>&times;</button>
                                            </div>
                                        @empty
                                            <div class="cms-section-row cms-wide-row">
                                                <input type="text" name="trust_icons[]" placeholder="Icon/number">
                                                <input type="text" name="trust_titles[]" placeholder="Title">
                                                <textarea name="trust_texts[]" rows="2" placeholder="Text"></textarea>
                                                <button type="button" data-remove-row>&times;</button>
                                            </div>
                                        @endforelse
                                    </div>
                                    <button type="button" class="cms-secondary-button" data-add-row>Add Trust Item</button>
                                    <div class="cms-two">
                                        <label>
                                            Rating Text
                                            <textarea name="rating_text" rows="2">{{ $page['rating_text'] ?? '' }}</textarea>
                                        </label>
                                        <label>
                                            Rating CTA
                                            <input type="text" name="rating_cta_label" value="{{ $page['rating_cta_label'] ?? '' }}" placeholder="Label">
                                            <input type="text" name="rating_cta_url" value="{{ $page['rating_cta_url'] ?? '' }}" placeholder="URL">
                                        </label>
                                    </div>
                                </div>

                                <div class="cms-home-group" id="block-services">
                                    <h4>Services and Event Headings</h4>
                                    <div class="cms-two">
                                        <label>
                                            Services Heading
                                            <input type="text" name="services_heading" value="{{ $page['services_heading'] ?? '' }}">
                                        </label>
                                        <label>
                                            Events Heading
                                            <input type="text" name="events_heading" value="{{ $page['events_heading'] ?? '' }}">
                                        </label>
                                    </div>
                                </div>

                                <div class="cms-home-group" id="block-events">
                                    <h4>Events</h4>
                                    <div class="cms-section-list" data-section-list>
                                        @forelse($events as $event)
                                            <div class="cms-section-row cms-wide-row">
                                                <input type="text" name="event_kickers[]" value="{{ $event['kicker'] ?? '' }}" placeholder="Kicker">
                                                <input type="text" name="event_titles[]" value="{{ $event['title'] ?? '' }}" placeholder="Title">
                                                <input type="text" name="event_poster_titles[]" value="{{ $event['poster_title'] ?? '' }}" placeholder="Poster title">
                                                <input type="text" name="event_meta[]" value="{{ $event['meta'] ?? '' }}" placeholder="Meta">
                                                <input type="text" name="event_images[]" value="{{ $event['image'] ?? '' }}" placeholder="Image path">
                                                <input type="text" name="event_button_labels[]" value="{{ $event['button_label'] ?? '' }}" placeholder="Button label">
                                                <input type="text" name="event_urls[]" value="{{ $event['url'] ?? '' }}" placeholder="URL">
                                                <button type="button" data-remove-row>&times;</button>
                                            </div>
                                        @empty
                                            <div class="cms-section-row cms-wide-row">
                                                <input type="text" name="event_kickers[]" placeholder="Kicker">
                                                <input type="text" name="event_titles[]" placeholder="Title">
                                                <input type="text" name="event_poster_titles[]" placeholder="Poster title">
                                                <input type="text" name="event_meta[]" placeholder="Meta">
                                                <input type="text" name="event_images[]" placeholder="Image path">
                                                <input type="text" name="event_button_labels[]" placeholder="Button label">
                                                <input type="text" name="event_urls[]" placeholder="URL">
                                                <button type="button" data-remove-row>&times;</button>
                                            </div>
                                        @endforelse
                                    </div>
                                    <button type="button" class="cms-secondary-button" data-add-row>Add Event</button>
                                </div>

                                <div class="cms-home-group" id="block-testimonials">
                                    <h4>Testimonials</h4>
                                    <div class="cms-two">
                                        <label>
                                            Heading
                                            <input type="text" name="testimonials_heading" value="{{ $page['testimonials_heading'] ?? '' }}">
                                        </label>
                                        <label>
                                            Intro
                                            <textarea name="testimonials_text" rows="2">{{ $page['testimonials_text'] ?? '' }}</textarea>
                                        </label>
                                    </div>
                                    <div class="cms-section-list" data-section-list>
                                        @forelse($testimonials as $testimonial)
                                            <div class="cms-section-row cms-wide-row">
                                                <textarea name="testimonial_texts[]" rows="2" placeholder="Testimonial">{{ $testimonial['text'] ?? '' }}</textarea>
                                                <input type="text" name="testimonial_authors[]" value="{{ $testimonial['author'] ?? '' }}" placeholder="Author">
                                                <button type="button" data-remove-row>&times;</button>
                                            </div>
                                        @empty
                                            <div class="cms-section-row cms-wide-row">
                                                <textarea name="testimonial_texts[]" rows="2" placeholder="Testimonial"></textarea>
                                                <input type="text" name="testimonial_authors[]" placeholder="Author">
                                                <button type="button" data-remove-row>&times;</button>
                                            </div>
                                        @endforelse
                                    </div>
                                    <button type="button" class="cms-secondary-button" data-add-row>Add Testimonial</button>
                                </div>

                                <div class="cms-home-group" id="block-requirements">
                                    <h4>Requirement Cards</h4>
                                    <label>
                                        Requirement Heading
                                        <input type="text" name="requirements_heading" value="{{ $page['requirements_heading'] ?? '' }}">
                                    </label>
                                    <div class="cms-section-list" data-section-list>
                                        @forelse($requirements as $requirement)
                                            <div class="cms-section-row cms-wide-row">
                                                <input type="text" name="requirement_titles[]" value="{{ $requirement['title'] ?? '' }}" placeholder="Title">
                                                <textarea name="requirement_texts[]" rows="2" placeholder="Text">{{ $requirement['text'] ?? '' }}</textarea>
                                                <textarea name="requirement_items[]" rows="4" placeholder="One checklist item per line">{{ implode("\n", $requirement['items'] ?? []) }}</textarea>
                                                <input type="text" name="requirement_button_labels[]" value="{{ $requirement['button_label'] ?? '' }}" placeholder="Button label">
                                                <input type="text" name="requirement_urls[]" value="{{ $requirement['url'] ?? '' }}" placeholder="URL">
                                                <button type="button" data-remove-row>&times;</button>
                                            </div>
                                        @empty
                                            <div class="cms-section-row cms-wide-row">
                                                <input type="text" name="requirement_titles[]" placeholder="Title">
                                                <textarea name="requirement_texts[]" rows="2" placeholder="Text"></textarea>
                                                <textarea name="requirement_items[]" rows="4" placeholder="One checklist item per line"></textarea>
                                                <input type="text" name="requirement_button_labels[]" placeholder="Button label">
                                                <input type="text" name="requirement_urls[]" placeholder="URL">
                                                <button type="button" data-remove-row>&times;</button>
                                            </div>
                                        @endforelse
                                    </div>
                                    <button type="button" class="cms-secondary-button" data-add-row>Add Requirement</button>
                                </div>

                                <div class="cms-home-group" id="block-updates">
                                    <h4>Update Cards</h4>
                                    <label>
                                        Updates Heading
                                        <input type="text" name="updates_heading" value="{{ $page['updates_heading'] ?? '' }}">
                                    </label>
                                    <div class="cms-section-list" data-section-list>
                                        @forelse($updates as $update)
                                            <div class="cms-section-row cms-wide-row">
                                                <input type="text" name="update_titles[]" value="{{ $update['title'] ?? '' }}" placeholder="Title">
                                                <textarea name="update_texts[]" rows="2" placeholder="Text">{{ $update['text'] ?? '' }}</textarea>
                                                <input type="text" name="update_images[]" value="{{ $update['image'] ?? '' }}" placeholder="Image path">
                                                <input type="text" name="update_link_labels[]" value="{{ $update['link_label'] ?? '' }}" placeholder="Link label">
                                                <input type="text" name="update_urls[]" value="{{ $update['url'] ?? '' }}" placeholder="URL">
                                                <button type="button" data-remove-row>&times;</button>
                                            </div>
                                        @empty
                                            <div class="cms-section-row cms-wide-row">
                                                <input type="text" name="update_titles[]" placeholder="Title">
                                                <textarea name="update_texts[]" rows="2" placeholder="Text"></textarea>
                                                <input type="text" name="update_images[]" placeholder="Image path">
                                                <input type="text" name="update_link_labels[]" placeholder="Link label">
                                                <input type="text" name="update_urls[]" placeholder="URL">
                                                <button type="button" data-remove-row>&times;</button>
                                            </div>
                                        @endforelse
                                    </div>
                                    <button type="button" class="cms-secondary-button" data-add-row>Add Update</button>
                                </div>

                                <div class="cms-home-group" id="block-cta">
                                    <h4>CTA, Offices, Footer</h4>
                                    <div class="cms-two">
                                        <label>
                                            CTA Title
                                            <input type="text" name="cta_title" value="{{ $page['cta_title'] ?? '' }}">
                                        </label>
                                        <label>
                                            CTA Image
                                            <input type="text" name="cta_image" value="{{ $page['cta_image'] ?? '' }}">
                                        </label>
                                    </div>
                                    <label>
                                        CTA Text
                                        <textarea name="cta_text" rows="2">{{ $page['cta_text'] ?? '' }}</textarea>
                                    </label>
                                    <div class="cms-two">
                                        <label>
                                            CTA Button Label
                                            <input type="text" name="cta_button_label" value="{{ $page['cta_button_label'] ?? '' }}">
                                        </label>
                                        <label>
                                            CTA URL
                                            <input type="text" name="cta_url" value="{{ $page['cta_url'] ?? '' }}">
                                        </label>
                                    </div>
                                    <div class="cms-two">
                                        <label>
                                            Office Heading
                                            <input type="text" name="office_heading" value="{{ $page['office_heading'] ?? '' }}">
                                        </label>
                                        <label>
                                            Locations
                                            <input type="text" name="locations" value="{{ $locations }}" placeholder="Sydney, Melbourne">
                                        </label>
                                    </div>
                                    <label>
                                        Office Text
                                        <textarea name="office_text" rows="2">{{ $page['office_text'] ?? '' }}</textarea>
                                    </label>
                                    <div class="cms-section-list" data-section-list>
                                        @forelse($offices as $office)
                                            <div class="cms-section-row cms-wide-row">
                                                <input type="text" name="office_titles[]" value="{{ $office['title'] ?? '' }}" placeholder="Office title">
                                                <textarea name="office_texts[]" rows="2" placeholder="Description">{{ $office['text'] ?? '' }}</textarea>
                                                <input type="text" name="office_emails[]" value="{{ $office['email'] ?? '' }}" placeholder="Email">
                                                <input type="text" name="office_phones[]" value="{{ $office['phone'] ?? '' }}" placeholder="Phone">
                                                <button type="button" data-remove-row>&times;</button>
                                            </div>
                                        @empty
                                            <div class="cms-section-row cms-wide-row">
                                                <input type="text" name="office_titles[]" placeholder="Office title">
                                                <textarea name="office_texts[]" rows="2" placeholder="Description"></textarea>
                                                <input type="text" name="office_emails[]" placeholder="Email">
                                                <input type="text" name="office_phones[]" placeholder="Phone">
                                                <button type="button" data-remove-row>&times;</button>
                                            </div>
                                        @endforelse
                                    </div>
                                    <button type="button" class="cms-secondary-button" data-add-row>Add Office</button>
                                    <div class="cms-two">
                                        <label>
                                            Footer Text
                                            <textarea name="footer_text" rows="2">{{ $page['footer_text'] ?? '' }}</textarea>
                                        </label>
                                        <label>
                                            Footer Credit
                                            <input type="text" name="footer_credit" value="{{ $page['footer_credit'] ?? '' }}">
                                        </label>
                                    </div>
                                </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="cms-actions">
                            <button type="button" class="cms-secondary-button" data-add-row>Add Section</button>
                            <button type="submit" class="cms-button">Save Page</button>
                        </div>
                    </form>
                </details>
            @endforeach
        </div>
    </section>

    <section class="cms-panel cms-builder-stage" id="service-library">
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
                        <span><small>Service</small>{{ $service['label'] ?? $slug }}</span>
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
        </main>
    </div>
</div>

<style>
    .cms-admin { display: grid; gap: 24px; }
    .cms-builder-shell { display: grid; grid-template-columns: 260px minmax(0, 1fr); gap: 22px; align-items: start; }
    .cms-builder-sidebar { position: sticky; top: 88px; display: grid; gap: 18px; padding: 18px; border: 1px solid #e5e7eb; border-radius: 8px; background: #111827; color: #fff; box-shadow: 0 18px 34px rgba(17,24,39,.14); }
    .cms-builder-brand { display: flex; align-items: center; gap: 12px; padding-bottom: 14px; border-bottom: 1px solid rgba(255,255,255,.14); }
    .cms-builder-brand img { width: 54px; height: 54px; object-fit: contain; padding: 6px; border-radius: 8px; background: #fff; }
    .cms-builder-brand strong, .cms-builder-brand span { display: block; }
    .cms-builder-brand strong { font-size: 15px; line-height: 1.2; }
    .cms-builder-brand span { margin-top: 3px; color: #cbd5e1; font-size: 12px; }
    .cms-builder-sidebar nav { display: grid; gap: 8px; }
    .cms-builder-sidebar nav a, .cms-sidebar-preview { display: flex; align-items: center; justify-content: space-between; min-height: 42px; padding: 10px 12px; border-radius: 8px; color: #e5e7eb; background: rgba(255,255,255,.06); text-decoration: none; font-weight: 800; font-size: 13px; }
    .cms-builder-sidebar nav a:hover, .cms-sidebar-preview:hover { color: #fff; background: var(--accent); }
    .cms-sidebar-preview { background: #fff; color: #111827; }
    .cms-builder-main { display: grid; gap: 24px; min-width: 0; }
    .cms-builder-stage { scroll-margin-top: 90px; }
    .cms-stage-title { display: flex; justify-content: space-between; gap: 16px; align-items: center; margin-bottom: 14px; padding: 18px 20px; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,.06); }
    .cms-stage-title h2, .cms-stage-title p { margin: 0; }
    .cms-stage-title h2 { color: #111827; font-size: 22px; }
    .cms-stage-title p { margin-top: 4px; color: #6b7280; font-size: 13px; }
    .cms-stage-title a { color: var(--accent); font-weight: 900; text-decoration: none; }
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
    .cms-editor summary span { display: grid; gap: 3px; }
    .cms-editor summary small { color: #6b7280; font-size: 11px; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; }
    .cms-editor summary a { color: var(--accent); font-size: 13px; text-decoration: none; }
    .cms-section-list { display: grid; gap: 12px; }
    .cms-section-row { display: grid; grid-template-columns: minmax(160px, 260px) 1fr 40px; gap: 10px; align-items: stretch; }
    .cms-section-row.cms-wide-row { grid-template-columns: repeat(2, minmax(160px, 1fr)) 40px; }
    .cms-section-row.cms-wide-row textarea { grid-column: span 2; }
    .cms-section-row.cms-wide-row button { grid-column: -2 / -1; grid-row: 1 / span 2; }
    .cms-section-row.cms-one-input-row { grid-template-columns: 1fr 40px; }
    .cms-section-row button { border: none; border-radius: 8px; background: #fee2e2; color: #b91c1c; font-size: 22px; cursor: pointer; }
    .cms-actions { display: flex; justify-content: flex-end; gap: 12px; flex-wrap: wrap; }
    .cms-home-builder { display: grid; gap: 18px; padding-top: 8px; border-top: 1px solid #e5e7eb; scroll-margin-top: 90px; }
    .cms-builder-top { display: flex; align-items: center; justify-content: space-between; gap: 14px; padding: 16px 18px; border: 1px solid #dbeafe; border-radius: 8px; background: linear-gradient(135deg, #eff6ff, #fff); }
    .cms-builder-top span { color: #2563eb; font-size: 12px; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; }
    .cms-builder-top h3 { margin: 4px 0 0; color: #111827; font-size: 20px; }
    .cms-builder-top a { color: var(--accent); font-weight: 900; text-decoration: none; }
    .cms-home-layout { display: grid; grid-template-columns: 190px minmax(0, 1fr); gap: 16px; align-items: start; }
    .cms-section-navigator { position: sticky; top: 88px; display: grid; gap: 8px; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px; background: #f8fafc; }
    .cms-section-navigator a { padding: 10px 12px; border-radius: 8px; color: #374151; text-decoration: none; font-size: 13px; font-weight: 900; }
    .cms-section-navigator a:hover { color: #fff; background: var(--accent); }
    .cms-inspector-stack { display: grid; gap: 16px; min-width: 0; }
    .cms-home-builder h3, .cms-home-group h4 { margin: 0; color: #111827; }
    .cms-home-builder h3 { font-size: 18px; }
    .cms-home-group { display: grid; gap: 14px; padding: 16px; border: 1px solid #e5e7eb; border-radius: 8px; background: #fbfdff; scroll-margin-top: 90px; }
    .cms-home-group h4 { font-size: 15px; }
    .cms-alert { padding: 16px; border-radius: 8px; font-weight: 700; }
    .cms-alert ul { margin: 0; padding-left: 20px; }
    .cms-alert-success { background: #ecfdf5; color: #047857; border: 1px solid #bbf7d0; }
    .cms-alert-danger { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
    .cms-preview-logo { width: 120px; max-height: 120px; object-fit: contain; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px; }
    .cms-image-preview { display: grid; gap: 8px; width: min(100%, 180px); margin-top: 8px; padding: 8px; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; }
    .cms-image-preview img { width: 100%; aspect-ratio: 16 / 10; object-fit: cover; border-radius: 6px; background: #f3f4f6; }
    .cms-image-preview span { overflow: hidden; color: #6b7280; font-size: 11px; font-weight: 800; text-overflow: ellipsis; white-space: nowrap; }
    .cms-image-preview.is-empty { display: none; }
    @media (max-width: 1100px) { .cms-builder-shell, .cms-home-layout { grid-template-columns: 1fr; } .cms-builder-sidebar, .cms-section-navigator { position: static; } .cms-section-navigator { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 1000px) { .cms-grid, .cms-two, .cms-section-row, .cms-section-row.cms-wide-row { grid-template-columns: 1fr; } .cms-section-row.cms-wide-row textarea, .cms-section-row.cms-wide-row button { grid-column: auto; grid-row: auto; } .cms-section-row button { height: 40px; } }
</style>

<script>
    const cmsAssetBase = @json(rtrim(asset(''), '/') . '/');
    const cmsImagePathSelector = [
        'input[name="logo_path"]',
        'input[name="hero_image_path"]',
        'input[name="pathway_images[]"]',
        'input[name="event_images[]"]',
        'input[name="update_images[]"]',
        'input[name="cta_image"]',
        'input[name="image_path"]'
    ].join(',');

    function cmsImageUrl(path) {
        const value = (path || '').trim();
        if (!value) return '';
        if (/^(https?:)?\/\//i.test(value) || value.startsWith('data:') || value.startsWith('blob:')) return value;
        return cmsAssetBase + value.replace(/^\/+/, '');
    }

    function cmsEnsureImagePreview(input) {
        let preview = input.parentElement.querySelector(':scope > .cms-image-preview');

        if (!preview) {
            preview = document.createElement('div');
            preview.className = 'cms-image-preview is-empty';
            preview.setAttribute('data-auto-image-preview', '');
            preview.innerHTML = '<img alt="Image preview"><span></span>';
            input.insertAdjacentElement('afterend', preview);
        }

        return preview;
    }

    function cmsUpdateImagePreview(input, forcedUrl, forcedLabel) {
        const preview = cmsEnsureImagePreview(input);
        const image = preview.querySelector('img');
        const label = preview.querySelector('span');
        const path = input.value || '';
        const url = forcedUrl || cmsImageUrl(path);

        if (!url) {
            preview.classList.add('is-empty');
            image.removeAttribute('src');
            label.textContent = '';
            return;
        }

        image.src = url;
        label.textContent = forcedLabel || path;
        preview.classList.remove('is-empty');
    }

    function cmsHydrateImagePreviews(root) {
        root.querySelectorAll(cmsImagePathSelector).forEach(function(input) {
            cmsUpdateImagePreview(input);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        cmsHydrateImagePreviews(document);
    });

    document.addEventListener('input', function(event) {
        if (event.target.matches('input[type="color"]')) {
            const text = document.querySelector('[data-color-text="' + event.target.name + '"]');
            if (text) text.value = event.target.value;
        }

        if (event.target.matches('[data-color-text]')) {
            const color = document.querySelector('input[type="color"][name="' + event.target.dataset.colorText + '"]');
            if (color && /^#[0-9A-Fa-f]{6}$/.test(event.target.value)) color.value = event.target.value;
        }

        if (event.target.matches(cmsImagePathSelector)) {
            cmsUpdateImagePreview(event.target);
        }
    });

    document.addEventListener('change', function(event) {
        if (!event.target.matches('input[type="file"][accept^="image"]') || !event.target.files || !event.target.files[0]) {
            return;
        }

        const form = event.target.closest('form');
        const pairs = {
            logo_upload: 'input[name="logo_path"]',
            hero_image_upload: 'input[name="hero_image_path"]',
            image_upload: 'input[name="image_path"]'
        };
        const selector = pairs[event.target.name];
        const pathInput = selector && form ? form.querySelector(selector) : null;

        if (pathInput) {
            cmsUpdateImagePreview(pathInput, URL.createObjectURL(event.target.files[0]), event.target.files[0].name);
        }
    });

    document.addEventListener('click', function(event) {
        if (event.target.matches('[data-add-row]')) {
            const container = event.target.closest('.cms-home-group') || event.target.closest('form');
            const list = container.querySelector('[data-section-list]');
            const first = list.querySelector('.cms-section-row');
            const clone = first.cloneNode(true);
            clone.querySelectorAll('input, textarea').forEach(function(input) { input.value = ''; });
            clone.querySelectorAll('[data-auto-image-preview]').forEach(function(preview) { preview.remove(); });
            list.appendChild(clone);
            cmsHydrateImagePreviews(clone);
        }

        if (event.target.matches('[data-remove-row]')) {
            const row = event.target.closest('.cms-section-row');
            const list = event.target.closest('[data-section-list]');
            if (list.querySelectorAll('.cms-section-row').length > 1) {
                row.remove();
            } else {
                row.querySelectorAll('input, textarea').forEach(function(input) { input.value = ''; });
                cmsHydrateImagePreviews(row);
            }
        }
    });
</script>
@endsection
