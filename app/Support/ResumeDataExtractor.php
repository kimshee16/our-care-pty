<?php

namespace App\Support;

class ResumeDataExtractor
{
    public function extract(string $text, array $skillOptions = []): array
    {
        $text = $this->normalizeText($text);
        $lines = $this->lines($text);

        $email = $this->match('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $text);
        $phone = $this->match('/(?:\+?\d[\d\s().\-]{7,}\d)/', $text);
        $name = $this->extractName($lines, $email);
        $profession = $this->extractProfession($text);
        $skills = $this->extractSkills($text, $skillOptions);

        return array_filter([
            'firstName' => $name['firstName'] ?? null,
            'lastName' => $name['lastName'] ?? null,
            'email' => $email,
            'phone' => $phone ? preg_replace('/\s+/', ' ', trim($phone)) : null,
            'profession' => $profession,
            'specialization' => $this->extractSpecialization($text),
            'licenseNumber' => $this->extractLicenseNumber($text),
            'experience' => $this->extractExperience($text),
            'facility' => $this->extractLabeledValue($text, ['current employer', 'employer', 'facility', 'hospital', 'clinic']),
            'location' => $this->extractLocation($text),
            'credentials' => $this->extractCredentials($text),
            'skills' => $skills ?: null,
        ], fn ($value) => $value !== null && $value !== '');
    }

    private function normalizeText(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace('/[ \t]+/', ' ', $text);

        return trim((string) $text);
    }

    private function lines(string $text): array
    {
        return collect(explode("\n", $text))
            ->map(fn ($line) => trim(preg_replace('/\s+/', ' ', $line)))
            ->filter()
            ->values()
            ->all();
    }

    private function match(string $pattern, string $text, int $group = 0): ?string
    {
        if (preg_match($pattern, $text, $matches)) {
            return trim($matches[$group] ?? $matches[0]);
        }

        return null;
    }

    private function extractName(array $lines, ?string $email): array
    {
        $emailIndex = null;

        if ($email) {
            foreach ($lines as $index => $line) {
                if (stripos($line, $email) !== false) {
                    $emailIndex = $index;
                    break;
                }
            }
        }

        $candidates = $emailIndex !== null
            ? array_slice($lines, max(0, $emailIndex - 3), 3)
            : array_slice($lines, 0, 6);

        foreach ($candidates as $line) {
            if ($this->looksLikeName($line)) {
                $parts = preg_split('/\s+/', $line);

                return [
                    'firstName' => $parts[0] ?? null,
                    'lastName' => count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : null,
                ];
            }
        }

        return [];
    }

    private function looksLikeName(string $line): bool
    {
        if (preg_match('/[@\d:|]/', $line)) {
            return false;
        }

        if (preg_match('/\b(resume|curriculum vitae|cv|profile|summary|experience|education|skills)\b/i', $line)) {
            return false;
        }

        $words = preg_split('/\s+/', trim($line));

        if (count($words) < 2 || count($words) > 4) {
            return false;
        }

        return collect($words)->every(fn ($word) => preg_match('/^[A-Z][A-Za-z\'-]+$/', $word));
    }

    private function extractProfession(string $text): ?string
    {
        $professionMap = [
            'nurse' => ['registered nurse', 'enrolled nurse', 'nursing assistant', 'nurse'],
            'doctor' => ['doctor', 'physician', 'medical officer', 'general practitioner', 'gp'],
            'physiotherapist' => ['physiotherapist', 'physical therapist', 'physio'],
            'psychologist' => ['psychologist', 'psychology'],
            'counselor' => ['counsellor', 'counselor', 'therapist'],
            'technician' => ['medical technician', 'technician'],
        ];

        foreach ($professionMap as $profession => $needles) {
            foreach ($needles as $needle) {
                if (preg_match('/\b' . preg_quote($needle, '/') . '\b/i', $text)) {
                    return $profession;
                }
            }
        }

        return null;
    }

    private function extractSpecialization(string $text): ?string
    {
        $labeled = $this->extractLabeledValue($text, ['specialization', 'specialisation', 'specialty', 'speciality']);

        if ($labeled) {
            return $labeled;
        }

        $specializations = [
            'Aged Care',
            'Disability Support',
            'Mental Health',
            'Community Care',
            'Cardiology',
            'Orthopedics',
            'Orthopaedics',
            'Pediatrics',
            'Paediatrics',
            'Rehabilitation',
            'Emergency',
            'Critical Care',
            'Home Care',
        ];

        foreach ($specializations as $specialization) {
            if (preg_match('/\b' . preg_quote($specialization, '/') . '\b/i', $text)) {
                return $specialization;
            }
        }

        return null;
    }

    private function extractLicenseNumber(string $text): ?string
    {
        $patterns = [
            '/(?:AHPRA(?:\s*(?:registration|reg)?(?:\s*(?:no|number|#))?)?|license(?:\s*(?:no|number|#))?|licence(?:\s*(?:no|number|#))?|registration(?:\s*(?:no|number|#))?|reg\s*(?:no|number|#))\s*[:#-]?\s*([A-Z0-9][A-Z0-9\-\/]{3,})/i',
            '/\b(?:RN|EN)\s*[:#-]?\s*([A-Z0-9][A-Z0-9\-\/]{3,})/i',
        ];

        foreach ($patterns as $pattern) {
            $value = $this->match($pattern, $text, 1);

            if ($value) {
                return $value;
            }
        }

        return null;
    }

    private function extractExperience(string $text): ?int
    {
        $patterns = [
            '/(\d{1,2})\+?\s*(?:years|yrs|year)\s+(?:of\s+)?(?:clinical\s+)?experience/i',
            '/experience\s*[:#-]?\s*(\d{1,2})\+?\s*(?:years|yrs|year)/i',
        ];

        foreach ($patterns as $pattern) {
            $value = $this->match($pattern, $text, 1);

            if ($value !== null) {
                return (int) $value;
            }
        }

        return null;
    }

    private function extractLocation(string $text): ?string
    {
        $labeled = $this->extractLabeledValue($text, ['location', 'address']);

        if ($labeled) {
            return $labeled;
        }

        return $this->match('/\b([A-Z][A-Za-z .\'-]+,\s*(?:NSW|VIC|QLD|SA|WA|TAS|ACT|NT)\s*\d{4}?)\b/', $text, 1)
            ?: $this->match('/\b([A-Z][A-Za-z .\'-]+\s+(?:NSW|VIC|QLD|SA|WA|TAS|ACT|NT)\s*\d{4}?)\b/', $text, 1);
    }

    private function extractCredentials(string $text): ?string
    {
        $sections = [];

        foreach (['professional summary', 'profile', 'qualifications', 'certifications', 'education'] as $heading) {
            $section = $this->extractSection($text, $heading);

            if ($section) {
                $sections[] = $section;
            }
        }

        $credentials = trim(implode("\n\n", array_unique($sections)));

        if ($credentials !== '') {
            return mb_substr($credentials, 0, 1200);
        }

        return mb_substr($text, 0, 800) ?: null;
    }

    private function extractSection(string $text, string $heading): ?string
    {
        $pattern = '/\b' . preg_quote($heading, '/') . '\b\s*:?\s*(.*?)(?=\n\s*(?:professional summary|profile|experience|employment|work history|skills|qualifications|certifications|education|references)\b|\z)/is';

        if (!preg_match($pattern, $text, $matches)) {
            return null;
        }

        $section = trim(preg_replace('/\n{3,}/', "\n\n", $matches[1] ?? ''));

        return $section !== '' ? $section : null;
    }

    private function extractSkills(string $text, array $skillOptions): array
    {
        $found = [];

        foreach ($skillOptions as $skill) {
            if (preg_match('/\b' . preg_quote($skill, '/') . '\b/i', $text)) {
                $found[] = $skill;
            }
        }

        return array_values(array_unique($found));
    }

    private function extractLabeledValue(string $text, array $labels): ?string
    {
        foreach ($labels as $label) {
            $value = $this->match('/\b' . preg_quote($label, '/') . '\b\s*[:#-]\s*([^\n]+)/i', $text, 1);

            if ($value) {
                return mb_substr(trim($value), 0, 255);
            }
        }

        return null;
    }
}
