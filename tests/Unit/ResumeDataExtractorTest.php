<?php

namespace Tests\Unit;

use App\Support\ResumeDataExtractor;
use PHPUnit\Framework\TestCase;

class ResumeDataExtractorTest extends TestCase
{
    public function test_it_extracts_common_healthcare_resume_fields(): void
    {
        $resumeText = <<<TEXT
        Ava Nguyen
        Registered Nurse
        ava.nguyen@example.test | 0400 000 000 | Sydney NSW 2000

        Professional Summary
        Registered nurse with 6 years of clinical experience in aged care and disability support.

        AHPRA Registration: NURSE-12345
        Current Employer: Our Care Clinic

        Skills
        Medication Management, Wound Care, Manual Handling

        Certifications
        Bachelor of Nursing
        First Aid Certificate
        TEXT;

        $fields = (new ResumeDataExtractor())->extract($resumeText, [
            'Medication Management',
            'Wound Care',
            'Manual Handling',
            'Palliative Care',
        ]);

        $this->assertSame('Ava', $fields['firstName']);
        $this->assertSame('Nguyen', $fields['lastName']);
        $this->assertSame('ava.nguyen@example.test', $fields['email']);
        $this->assertSame('0400 000 000', $fields['phone']);
        $this->assertSame('nurse', $fields['profession']);
        $this->assertSame('Aged Care', $fields['specialization']);
        $this->assertSame('NURSE-12345', $fields['licenseNumber']);
        $this->assertSame(6, $fields['experience']);
        $this->assertSame('Our Care Clinic', $fields['facility']);
        $this->assertSame('Sydney NSW 2000', $fields['location']);
        $this->assertContains('Medication Management', $fields['skills']);
        $this->assertContains('Wound Care', $fields['skills']);
        $this->assertContains('Manual Handling', $fields['skills']);
    }
}
