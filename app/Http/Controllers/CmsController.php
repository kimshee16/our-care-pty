<?php

namespace App\Http\Controllers;

use App\Support\CmsContent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CmsController extends Controller
{
    public function index()
    {
        return view('admin-cms', [
            'cms' => CmsContent::all(),
            'pageSlugs' => array_keys(config('cms.pages', [])),
            'services' => CmsContent::services(),
        ]);
    }

    public function updateBrand(Request $request)
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:180'],
            'logo_path' => ['nullable', 'string', 'max:255'],
            'logo_upload' => ['nullable', 'image', 'max:4096'],
            'phone' => ['nullable', 'string', 'max:80'],
            'email' => ['nullable', 'string', 'max:160'],
            'sign_in_label' => ['nullable', 'string', 'max:80'],
            'create_account_label' => ['nullable', 'string', 'max:80'],
            'updates_label' => ['nullable', 'string', 'max:80'],
            'updates_url' => ['nullable', 'string', 'max:255'],
            'footer_services_label' => ['nullable', 'string', 'max:80'],
            'footer_quick_links_label' => ['nullable', 'string', 'max:80'],
            'footer_contact_label' => ['nullable', 'string', 'max:80'],
        ]);

        CmsContent::set('brand', [
            'site_name' => trim($data['site_name']),
            'tagline' => trim((string) ($data['tagline'] ?? '')),
            'logo' => $this->storedAssetPath($request, 'logo_upload', $data['logo_path'] ?? config('cms.brand.logo')),
            'phone' => trim((string) ($data['phone'] ?? '')),
            'email' => trim((string) ($data['email'] ?? '')),
            'sign_in_label' => trim((string) ($data['sign_in_label'] ?? '')),
            'create_account_label' => trim((string) ($data['create_account_label'] ?? '')),
            'updates_label' => trim((string) ($data['updates_label'] ?? '')),
            'updates_url' => trim((string) ($data['updates_url'] ?? '')),
            'footer_services_label' => trim((string) ($data['footer_services_label'] ?? '')),
            'footer_quick_links_label' => trim((string) ($data['footer_quick_links_label'] ?? '')),
            'footer_contact_label' => trim((string) ($data['footer_contact_label'] ?? '')),
        ]);

        return redirect('/admin/cms')->with('status', 'Brand settings saved.');
    }

    public function updatePalette(Request $request)
    {
        $data = $request->validate([
            'primary' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'background' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'surface' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'text' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        CmsContent::set('palette', $data);

        return redirect('/admin/cms')->with('status', 'Color palette saved.');
    }

    public function updatePage(Request $request, string $slug)
    {
        abort_unless(array_key_exists($slug, config('cms.pages', [])), 404);

        $data = $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'title' => ['required', 'string', 'max:160'],
            'hero_title' => ['required', 'string', 'max:220'],
            'hero_subtitle' => ['nullable', 'string', 'max:500'],
            'hero_image_path' => ['nullable', 'string', 'max:255'],
            'hero_image_upload' => ['nullable', 'image', 'max:6144'],
            'intro_title' => ['nullable', 'string', 'max:160'],
            'intro_text' => ['nullable', 'string', 'max:2000'],
            'section_titles' => ['nullable', 'array'],
            'section_titles.*' => ['nullable', 'string', 'max:160'],
            'section_texts' => ['nullable', 'array'],
            'section_texts.*' => ['nullable', 'string', 'max:1200'],
            'hero_cta_label' => ['nullable', 'string', 'max:120'],
            'hero_cta_url' => ['nullable', 'string', 'max:255'],
            'hero_note' => ['nullable', 'string', 'max:500'],
            'intro_facts' => ['nullable', 'array'],
            'intro_facts.*' => ['nullable', 'string', 'max:220'],
            'pathway_titles' => ['nullable', 'array'],
            'pathway_titles.*' => ['nullable', 'string', 'max:160'],
            'pathway_texts' => ['nullable', 'array'],
            'pathway_texts.*' => ['nullable', 'string', 'max:500'],
            'pathway_images' => ['nullable', 'array'],
            'pathway_images.*' => ['nullable', 'string', 'max:255'],
            'pathway_urls' => ['nullable', 'array'],
            'pathway_urls.*' => ['nullable', 'string', 'max:255'],
            'trust_heading' => ['nullable', 'string', 'max:180'],
            'trust_text' => ['nullable', 'string', 'max:500'],
            'trust_icons' => ['nullable', 'array'],
            'trust_icons.*' => ['nullable', 'string', 'max:12'],
            'trust_titles' => ['nullable', 'array'],
            'trust_titles.*' => ['nullable', 'string', 'max:160'],
            'trust_texts' => ['nullable', 'array'],
            'trust_texts.*' => ['nullable', 'string', 'max:500'],
            'rating_text' => ['nullable', 'string', 'max:500'],
            'rating_cta_label' => ['nullable', 'string', 'max:120'],
            'rating_cta_url' => ['nullable', 'string', 'max:255'],
            'services_heading' => ['nullable', 'string', 'max:180'],
            'events_heading' => ['nullable', 'string', 'max:180'],
            'event_kickers' => ['nullable', 'array'],
            'event_kickers.*' => ['nullable', 'string', 'max:80'],
            'event_titles' => ['nullable', 'array'],
            'event_titles.*' => ['nullable', 'string', 'max:180'],
            'event_poster_titles' => ['nullable', 'array'],
            'event_poster_titles.*' => ['nullable', 'string', 'max:180'],
            'event_meta' => ['nullable', 'array'],
            'event_meta.*' => ['nullable', 'string', 'max:100'],
            'event_images' => ['nullable', 'array'],
            'event_images.*' => ['nullable', 'string', 'max:255'],
            'event_button_labels' => ['nullable', 'array'],
            'event_button_labels.*' => ['nullable', 'string', 'max:120'],
            'event_urls' => ['nullable', 'array'],
            'event_urls.*' => ['nullable', 'string', 'max:255'],
            'testimonials_heading' => ['nullable', 'string', 'max:180'],
            'testimonials_text' => ['nullable', 'string', 'max:500'],
            'testimonial_texts' => ['nullable', 'array'],
            'testimonial_texts.*' => ['nullable', 'string', 'max:600'],
            'testimonial_authors' => ['nullable', 'array'],
            'testimonial_authors.*' => ['nullable', 'string', 'max:120'],
            'requirements_heading' => ['nullable', 'string', 'max:180'],
            'requirement_titles' => ['nullable', 'array'],
            'requirement_titles.*' => ['nullable', 'string', 'max:160'],
            'requirement_texts' => ['nullable', 'array'],
            'requirement_texts.*' => ['nullable', 'string', 'max:600'],
            'requirement_items' => ['nullable', 'array'],
            'requirement_items.*' => ['nullable', 'string', 'max:1000'],
            'requirement_button_labels' => ['nullable', 'array'],
            'requirement_button_labels.*' => ['nullable', 'string', 'max:120'],
            'requirement_urls' => ['nullable', 'array'],
            'requirement_urls.*' => ['nullable', 'string', 'max:255'],
            'updates_heading' => ['nullable', 'string', 'max:180'],
            'update_titles' => ['nullable', 'array'],
            'update_titles.*' => ['nullable', 'string', 'max:180'],
            'update_texts' => ['nullable', 'array'],
            'update_texts.*' => ['nullable', 'string', 'max:600'],
            'update_images' => ['nullable', 'array'],
            'update_images.*' => ['nullable', 'string', 'max:255'],
            'update_link_labels' => ['nullable', 'array'],
            'update_link_labels.*' => ['nullable', 'string', 'max:120'],
            'update_urls' => ['nullable', 'array'],
            'update_urls.*' => ['nullable', 'string', 'max:255'],
            'cta_title' => ['nullable', 'string', 'max:180'],
            'cta_text' => ['nullable', 'string', 'max:500'],
            'cta_button_label' => ['nullable', 'string', 'max:120'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'cta_image' => ['nullable', 'string', 'max:255'],
            'office_heading' => ['nullable', 'string', 'max:180'],
            'office_text' => ['nullable', 'string', 'max:500'],
            'locations' => ['nullable', 'string', 'max:500'],
            'office_titles' => ['nullable', 'array'],
            'office_titles.*' => ['nullable', 'string', 'max:160'],
            'office_texts' => ['nullable', 'array'],
            'office_texts.*' => ['nullable', 'string', 'max:400'],
            'office_emails' => ['nullable', 'array'],
            'office_emails.*' => ['nullable', 'string', 'max:160'],
            'office_phones' => ['nullable', 'array'],
            'office_phones.*' => ['nullable', 'string', 'max:80'],
            'footer_text' => ['nullable', 'string', 'max:500'],
            'footer_credit' => ['nullable', 'string', 'max:160'],
        ]);

        $page = [
            'label' => trim($data['label']),
            'title' => trim($data['title']),
            'hero_title' => trim($data['hero_title']),
            'hero_subtitle' => trim((string) ($data['hero_subtitle'] ?? '')),
            'hero_image' => $this->storedAssetPath($request, 'hero_image_upload', $data['hero_image_path'] ?? data_get(config('cms.pages'), "$slug.hero_image")),
            'intro_title' => trim((string) ($data['intro_title'] ?? '')),
            'intro_text' => trim((string) ($data['intro_text'] ?? '')),
            'sections' => $this->sectionRows($data['section_titles'] ?? [], $data['section_texts'] ?? []),
        ];

        if ($slug === 'home-v2') {
            $page = array_merge($page, $this->homePageRows($data));
        }

        CmsContent::set("pages.$slug", $page);

        return redirect('/admin/cms#page-' . $slug)->with('status', 'Page content saved.');
    }

    public function updateService(Request $request, string $slug)
    {
        abort_unless(array_key_exists($slug, config('ourcare_v2.services', [])), 404);

        $current = CmsContent::services()[$slug] ?? config("ourcare_v2.services.$slug", []);
        $data = $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'title' => ['required', 'string', 'max:180'],
            'heading' => ['nullable', 'string', 'max:220'],
            'summary' => ['nullable', 'string', 'max:600'],
            'registration' => ['nullable', 'string', 'max:300'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'image_upload' => ['nullable', 'image', 'max:6144'],
            'intro' => ['nullable', 'string', 'max:4000'],
            'section_heading' => ['nullable', 'string', 'max:180'],
            'section_intro' => ['nullable', 'string', 'max:1200'],
            'item_titles' => ['nullable', 'array'],
            'item_titles.*' => ['nullable', 'string', 'max:160'],
            'item_texts' => ['nullable', 'array'],
            'item_texts.*' => ['nullable', 'string', 'max:1200'],
        ]);

        $service = array_merge($current, [
            'label' => trim($data['label']),
            'title' => trim($data['title']),
            'heading' => trim((string) ($data['heading'] ?? '')) ?: null,
            'summary' => trim((string) ($data['summary'] ?? '')),
            'registration' => trim((string) ($data['registration'] ?? '')),
            'image' => $this->storedAssetPath($request, 'image_upload', $data['image_path'] ?? ($current['image'] ?? 'hero.jpg')),
            'intro' => $this->paragraphs($data['intro'] ?? ''),
            'section_heading' => trim((string) ($data['section_heading'] ?? '')),
            'section_intro' => trim((string) ($data['section_intro'] ?? '')),
            'items' => $this->sectionRows($data['item_titles'] ?? [], $data['item_texts'] ?? []),
        ]);

        CmsContent::set("services.$slug", $service);

        return redirect('/admin/cms#service-' . $slug)->with('status', 'Service content saved.');
    }

    public function reset(Request $request)
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:120'],
        ]);

        CmsContent::reset($data['key']);

        return redirect('/admin/cms')->with('status', 'CMS section reset to default.');
    }

    public function home()
    {
        return view('home-progress', [
            'brand' => CmsContent::get('brand'),
            'palette' => CmsContent::get('palette'),
            'page' => CmsContent::page('home-v2'),
            'services' => CmsContent::services(),
        ]);
    }

    public function about()
    {
        return $this->publicPage('about-v2');
    }

    public function services()
    {
        return $this->publicPage('services-v2');
    }

    public function onboarding()
    {
        return $this->publicPage('onboarding-v2');
    }

    public function intake()
    {
        return $this->publicPage('intake-v2');
    }

    public function contact()
    {
        return $this->publicPage('contact-v2');
    }

    private function publicPage(string $slug)
    {
        abort_unless(array_key_exists($slug, config('cms.pages', [])), 404);

        $payload = [
            'brand' => CmsContent::get('brand'),
            'palette' => CmsContent::get('palette'),
            'page' => CmsContent::page($slug),
            'pageSlug' => $slug,
            'pages' => CmsContent::get('pages', config('cms.pages', [])),
            'services' => CmsContent::services(),
        ];

        return view('cms-public-page', $payload);
    }

    public function serviceDetail(string $service)
    {
        $services = CmsContent::services();

        abort_unless(isset($services[$service]), 404);

        return view('cms-public-page', [
            'brand' => CmsContent::get('brand'),
            'palette' => CmsContent::get('palette'),
            'page' => $this->serviceAsPage($services[$service]),
            'pageSlug' => 'services-v2',
            'serviceSlug' => $service,
            'pages' => CmsContent::get('pages', config('cms.pages', [])),
            'services' => $services,
        ]);
    }

    private function serviceAsPage(array $service): array
    {
        $intro = implode("\n\n", $service['intro'] ?? []);

        return [
            'label' => $service['label'] ?? 'Service',
            'title' => $service['title'] ?? 'Service',
            'hero_title' => $service['title'] ?? 'Service',
            'hero_subtitle' => $service['summary'] ?? '',
            'hero_image' => $service['image'] ?? 'hero.jpg',
            'intro_title' => $service['heading'] ?? ($service['section_heading'] ?? ''),
            'intro_text' => $intro,
            'registration' => $service['registration'] ?? '',
            'section_heading' => $service['section_heading'] ?? '',
            'section_intro' => $service['section_intro'] ?? '',
            'sections' => $service['items'] ?? [],
        ];
    }

    private function storedAssetPath(Request $request, string $field, ?string $fallback): string
    {
        if (! $request->hasFile($field)) {
            return trim((string) $fallback);
        }

        $file = $request->file($field);
        $directory = public_path('cms');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = $file->getClientOriginalExtension();
        $filename = ($name ?: 'asset') . '-' . now()->format('YmdHis') . '.' . $extension;

        $file->move($directory, $filename);

        return 'cms/' . $filename;
    }

    private function sectionRows(array $titles, array $texts): array
    {
        $rows = [];
        $count = max(count($titles), count($texts));

        for ($index = 0; $index < $count; $index++) {
            $title = trim((string) ($titles[$index] ?? ''));
            $text = trim((string) ($texts[$index] ?? ''));

            if ($title === '' && $text === '') {
                continue;
            }

            $rows[] = ['title' => $title, 'text' => $text];
        }

        return $rows;
    }

    private function homePageRows(array $data): array
    {
        return [
            'hero_cta_label' => trim((string) ($data['hero_cta_label'] ?? '')),
            'hero_cta_url' => trim((string) ($data['hero_cta_url'] ?? '')),
            'hero_note' => trim((string) ($data['hero_note'] ?? '')),
            'intro_facts' => $this->stringRows($data['intro_facts'] ?? []),
            'pathways' => $this->keyedRows([
                'title' => $data['pathway_titles'] ?? [],
                'text' => $data['pathway_texts'] ?? [],
                'image' => $data['pathway_images'] ?? [],
                'url' => $data['pathway_urls'] ?? [],
            ], ['title', 'text', 'image', 'url']),
            'trust_heading' => trim((string) ($data['trust_heading'] ?? '')),
            'trust_text' => trim((string) ($data['trust_text'] ?? '')),
            'trust_items' => $this->keyedRows([
                'icon' => $data['trust_icons'] ?? [],
                'title' => $data['trust_titles'] ?? [],
                'text' => $data['trust_texts'] ?? [],
            ], ['title', 'text', 'icon']),
            'rating_text' => trim((string) ($data['rating_text'] ?? '')),
            'rating_cta_label' => trim((string) ($data['rating_cta_label'] ?? '')),
            'rating_cta_url' => trim((string) ($data['rating_cta_url'] ?? '')),
            'services_heading' => trim((string) ($data['services_heading'] ?? '')),
            'events_heading' => trim((string) ($data['events_heading'] ?? '')),
            'events' => $this->keyedRows([
                'kicker' => $data['event_kickers'] ?? [],
                'title' => $data['event_titles'] ?? [],
                'poster_title' => $data['event_poster_titles'] ?? [],
                'meta' => $data['event_meta'] ?? [],
                'image' => $data['event_images'] ?? [],
                'button_label' => $data['event_button_labels'] ?? [],
                'url' => $data['event_urls'] ?? [],
            ], ['title', 'poster_title', 'url']),
            'testimonials_heading' => trim((string) ($data['testimonials_heading'] ?? '')),
            'testimonials_text' => trim((string) ($data['testimonials_text'] ?? '')),
            'testimonials' => $this->keyedRows([
                'text' => $data['testimonial_texts'] ?? [],
                'author' => $data['testimonial_authors'] ?? [],
            ], ['text', 'author']),
            'requirements_heading' => trim((string) ($data['requirements_heading'] ?? '')),
            'requirements' => $this->requirementsRows($data),
            'updates_heading' => trim((string) ($data['updates_heading'] ?? '')),
            'updates' => $this->keyedRows([
                'title' => $data['update_titles'] ?? [],
                'text' => $data['update_texts'] ?? [],
                'image' => $data['update_images'] ?? [],
                'link_label' => $data['update_link_labels'] ?? [],
                'url' => $data['update_urls'] ?? [],
            ], ['title', 'text', 'url']),
            'cta_title' => trim((string) ($data['cta_title'] ?? '')),
            'cta_text' => trim((string) ($data['cta_text'] ?? '')),
            'cta_button_label' => trim((string) ($data['cta_button_label'] ?? '')),
            'cta_url' => trim((string) ($data['cta_url'] ?? '')),
            'cta_image' => trim((string) ($data['cta_image'] ?? '')),
            'office_heading' => trim((string) ($data['office_heading'] ?? '')),
            'office_text' => trim((string) ($data['office_text'] ?? '')),
            'locations' => $this->commaRows($data['locations'] ?? ''),
            'offices' => $this->keyedRows([
                'title' => $data['office_titles'] ?? [],
                'text' => $data['office_texts'] ?? [],
                'email' => $data['office_emails'] ?? [],
                'phone' => $data['office_phones'] ?? [],
            ], ['title', 'text', 'email', 'phone']),
            'footer_text' => trim((string) ($data['footer_text'] ?? '')),
            'footer_credit' => trim((string) ($data['footer_credit'] ?? '')),
        ];
    }

    private function keyedRows(array $columns, array $requiredKeys = []): array
    {
        $rows = [];
        $count = max(array_map('count', $columns) ?: [0]);

        for ($index = 0; $index < $count; $index++) {
            $row = [];
            foreach ($columns as $key => $values) {
                $row[$key] = trim((string) ($values[$index] ?? ''));
            }

            $hasRequiredValue = collect($requiredKeys)->contains(fn ($key) => ($row[$key] ?? '') !== '');
            if ($hasRequiredValue || collect($row)->contains(fn ($value) => $value !== '')) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    private function requirementsRows(array $data): array
    {
        $rows = $this->keyedRows([
            'title' => $data['requirement_titles'] ?? [],
            'text' => $data['requirement_texts'] ?? [],
            'items' => $data['requirement_items'] ?? [],
            'button_label' => $data['requirement_button_labels'] ?? [],
            'url' => $data['requirement_urls'] ?? [],
        ], ['title', 'text', 'url']);

        return array_map(function (array $row) {
            $row['items'] = $this->lineRows($row['items'] ?? '');

            return $row;
        }, $rows);
    }

    private function stringRows(array $values): array
    {
        return array_values(array_filter(array_map(
            fn ($value) => trim((string) $value),
            $values
        ), fn ($value) => $value !== ''));
    }

    private function commaRows(string $value): array
    {
        return array_values(array_filter(array_map('trim', explode(',', $value)), fn ($item) => $item !== ''));
    }

    private function lineRows(string $value): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', $value) ?: []), fn ($item) => $item !== ''));
    }

    private function paragraphs(string $value): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R{2,}/', $value) ?: [])));
    }
}
