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
        ]);

        CmsContent::set('brand', [
            'site_name' => trim($data['site_name']),
            'tagline' => trim((string) ($data['tagline'] ?? '')),
            'logo' => $this->storedAssetPath($request, 'logo_upload', $data['logo_path'] ?? config('cms.brand.logo')),
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
        ]);

        CmsContent::set("pages.$slug", [
            'label' => trim($data['label']),
            'title' => trim($data['title']),
            'hero_title' => trim($data['hero_title']),
            'hero_subtitle' => trim((string) ($data['hero_subtitle'] ?? '')),
            'hero_image' => $this->storedAssetPath($request, 'hero_image_upload', $data['hero_image_path'] ?? data_get(config('cms.pages'), "$slug.hero_image")),
            'intro_title' => trim((string) ($data['intro_title'] ?? '')),
            'intro_text' => trim((string) ($data['intro_text'] ?? '')),
            'sections' => $this->sectionRows($data['section_titles'] ?? [], $data['section_texts'] ?? []),
        ]);

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
        return $this->publicPage('home-v2');
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

    private function paragraphs(string $value): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R{2,}/', $value) ?: [])));
    }
}
