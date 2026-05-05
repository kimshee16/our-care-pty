@extends('layouts.dashboard')

@section('page-title', 'Post a Job')

@section('content')
@php
    $healthcareSkills = config('healthcare_skills.options', []);
    $oldKeySkills = old('key_skills', ['']);
    if (empty($oldKeySkills)) {
        $oldKeySkills = [''];
    }
@endphp
<div class="dashboard-content">
    <div class="dashboard-header">
        <h1>Post a Job</h1>
    </div>

    @if(session('status'))
        <div style="background: #d1fae5; color: #064e3b; padding: 16px; border-radius: 10px; border: 1px solid #a7f3d0; margin-bottom: 20px;">
            {{ session('status') }}
        </div>
    @endif

    <form action="/client/job-postings" method="POST" style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); border: 1px solid #e9ecef;">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
            <label>
                <span style="font-weight: 600;">Job Title</span>
                <input type="text" name="title" value="{{ old('title') }}" required style="width: 100%; padding: 12px; border: 1px solid #e6e6ee; border-radius: 10px; font-size: 14px; margin-top: 6px;">
                @error('title')<div style="color: #dc2626; margin-top: 4px; font-size: 13px;">{{ $message }}</div>@enderror
            </label>

            <label>
                <span style="font-weight: 600;">Description</span>
                <textarea name="description" rows="5" required style="width: 100%; padding: 12px; border: 1px solid #e6e6ee; border-radius: 10px; font-size: 14px; margin-top: 6px;">{{ old('description') }}</textarea>
                @error('description')<div style="color: #dc2626; margin-top: 4px; font-size: 13px;">{{ $message }}</div>@enderror
            </label>

            <div style="display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
                <label>
                    <span style="font-weight: 600;">Minimum Pay Offer (USD)</span>
                    <input type="number" step="0.01" name="minimum_pay_offer" value="{{ old('minimum_pay_offer') }}" style="width: 100%; padding: 12px; border: 1px solid #e6e6ee; border-radius: 10px; font-size: 14px; margin-top: 6px;">
                    @error('minimum_pay_offer')<div style="color: #dc2626; margin-top: 4px; font-size: 13px;">{{ $message }}</div>@enderror
                </label>
                <label>
                    <span style="font-weight: 600;">Maximum Pay Offer (USD)</span>
                    <input type="number" step="0.01" name="maximum_pay_offer" value="{{ old('maximum_pay_offer') }}" style="width: 100%; padding: 12px; border: 1px solid #e6e6ee; border-radius: 10px; font-size: 14px; margin-top: 6px;">
                    @error('maximum_pay_offer')<div style="color: #dc2626; margin-top: 4px; font-size: 13px;">{{ $message }}</div>@enderror
                </label>
            </div>

            <label>
                <span style="font-weight: 600;">Location</span>
                <input type="text" id="jobLocationInput" name="location" value="{{ old('location') }}" placeholder="Enter location" autocomplete="off" style="width: 100%; padding: 12px; border: 1px solid #e6e6ee; border-radius: 10px; font-size: 14px; margin-top: 6px;">
                @error('location')<div style="color: #dc2626; margin-top: 4px; font-size: 13px;">{{ $message }}</div>@enderror
            </label>

            <div style="display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
                <label>
                    <span style="font-weight: 600;">Employment Type</span>
                    <select name="employment_type" style="width: 100%; padding: 12px; border: 1px solid #e6e6ee; border-radius: 10px; font-size: 14px; margin-top: 6px;">
                        <option value="">Select Employment Type</option>
                        <option value="Full-time" {{ old('employment_type') === 'Full-time' ? 'selected' : '' }}>Full-time</option>
                        <option value="Part-time" {{ old('employment_type') === 'Part-time' ? 'selected' : '' }}>Part-time</option>
                    </select>
                    @error('employment_type')<div style="color: #dc2626; margin-top: 4px; font-size: 13px;">{{ $message }}</div>@enderror
                </label>
                <label>
                    <span style="font-weight: 600;">Experience</span>
                    <select name="experience" style="width: 100%; padding: 12px; border: 1px solid #e6e6ee; border-radius: 10px; font-size: 14px; margin-top: 6px;">
                        <option value="">Select Experience</option>
                        <option value="1 year" {{ old('experience') === '1 year' ? 'selected' : '' }}>1 year</option>
                        <option value="2 years" {{ old('experience') === '2 years' ? 'selected' : '' }}>2 years</option>
                        <option value="3 years" {{ old('experience') === '3 years' ? 'selected' : '' }}>3 years</option>
                        <option value="4 years" {{ old('experience') === '4 years' ? 'selected' : '' }}>4 years</option>
                        <option value="5 years" {{ old('experience') === '5 years' ? 'selected' : '' }}>5 years</option>
                        <option value="more than 5 years" {{ old('experience') === 'more than 5 years' ? 'selected' : '' }}>more than 5 years</option>
                    </select>
                    @error('experience')<div style="color: #dc2626; margin-top: 4px; font-size: 13px;">{{ $message }}</div>@enderror
                </label>
            </div>

            <label>
                <span style="font-weight: 600;">Specialty</span>
                <select name="specialty" style="width: 100%; padding: 12px; border: 1px solid #e6e6ee; border-radius: 10px; font-size: 14px; margin-top: 6px;">
                    <option value="">Select Specialty</option>
                    <option value="Nursing" {{ old('specialty') === 'Nursing' ? 'selected' : '' }}>Nursing</option>
                    <option value="Critical Care" {{ old('specialty') === 'Critical Care' ? 'selected' : '' }}>Critical Care</option>
                    <option value="Emergency Medicine" {{ old('specialty') === 'Emergency Medicine' ? 'selected' : '' }}>Emergency Medicine</option>
                    <option value="Radiology" {{ old('specialty') === 'Radiology' ? 'selected' : '' }}>Radiology</option>
                    <option value="Cardiology" {{ old('specialty') === 'Cardiology' ? 'selected' : '' }}>Cardiology</option>
                    <option value="Pediatrics" {{ old('specialty') === 'Pediatrics' ? 'selected' : '' }}>Pediatrics</option>
                    <option value="Oncology" {{ old('specialty') === 'Oncology' ? 'selected' : '' }}>Oncology</option>
                    <option value="Surgery" {{ old('specialty') === 'Surgery' ? 'selected' : '' }}>Surgery</option>
                    <option value="Mental Health" {{ old('specialty') === 'Mental Health' ? 'selected' : '' }}>Mental Health</option>
                    <option value="Physiotherapy" {{ old('specialty') === 'Physiotherapy' ? 'selected' : '' }}>Physiotherapy</option>
                </select>
                @error('specialty')<div style="color: #dc2626; margin-top: 4px; font-size: 13px;">{{ $message }}</div>@enderror
            </label>

            <div>
                <span style="font-weight: 600; display: block;">Key Skills</span>
                <div style="font-size: 13px; color: #6b7280; margin-top: 6px; margin-bottom: 8px;">Search and select only skills from the healthcare catalog.</div>
                <div id="jobKeySkillFields">
                    @foreach($oldKeySkills as $index => $skill)
                        <div class="job-key-skill-row" style="display: grid; grid-template-columns: 1fr auto; gap: 10px; margin-top: 6px;">
                            <input type="text" name="key_skills[]" list="jobPostingHealthcareSkillsList" placeholder="Search healthcare skills" value="{{ $skill }}" autocomplete="off" style="width: 100%; padding: 12px; border: 1px solid #e6e6ee; border-radius: 10px; font-size: 14px;">
                            <button type="button" class="{{ $index === 0 ? 'add-key-skill-btn' : 'remove-key-skill-btn' }}" style="padding: 12px 16px; border: none; border-radius: 10px; color: white; cursor: pointer; background: {{ $index === 0 ? 'var(--accent)' : '#dc3545' }};">{{ $index === 0 ? '+' : '-' }}</button>
                        </div>
                    @endforeach
                </div>
                <datalist id="jobPostingHealthcareSkillsList">
                    @foreach($healthcareSkills as $skillOption)
                        <option value="{{ $skillOption }}"></option>
                    @endforeach
                </datalist>
                @error('key_skills')<div style="color: #dc2626; margin-top: 4px; font-size: 13px;">{{ $message }}</div>@enderror
                @error('key_skills.*')<div style="color: #dc2626; margin-top: 4px; font-size: 13px;">{{ $message }}</div>@enderror
            </div>

            <label>
                <span style="font-weight: 600;">Key Requirements</span>
                <textarea name="requirements" rows="5" placeholder="One requirement per line" style="width: 100%; padding: 12px; border: 1px solid #e6e6ee; border-radius: 10px; font-size: 14px; margin-top: 6px;">{{ old('requirements') }}</textarea>
                @error('requirements')<div style="color: #dc2626; margin-top: 4px; font-size: 13px;">{{ $message }}</div>@enderror
            </label>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <a href="/client-dashboard" style="padding: 12px 20px; border-radius: 10px; background: #e5e7eb; color: #1f2937; text-decoration: none; font-weight: 600;">Cancel</a>
                <button type="submit" style="padding: 12px 20px; border-radius: 10px; background: var(--accent); color: white; border: none; font-weight: 600; cursor: pointer;">Create Job Post</button>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const jobPostingForm = document.querySelector('form[action="/client/job-postings"]');
        const jobKeySkillFields = document.getElementById('jobKeySkillFields');
        const jobSkillsList = document.getElementById('jobPostingHealthcareSkillsList');
        const allowedSkills = new Set(Array.from(jobSkillsList.options).map(option => option.value.trim()));
        const jobLocationInput = document.getElementById('jobLocationInput');

        function validateSkillInput(input) {
            const value = input.value.trim();

            if (value === '' || allowedSkills.has(value)) {
                input.setCustomValidity('');
                return true;
            }

            input.setCustomValidity('Please select a skill from the healthcare skills list.');
            return false;
        }

        function clearInvalidSkillInput(input) {
            if (validateSkillInput(input)) {
                return true;
            }

            input.value = '';
            input.setCustomValidity('');
            return false;
        }

        function attachSkillValidation(input) {
            input.addEventListener('input', function () {
                validateSkillInput(input);
            });

            input.addEventListener('change', function () {
                clearInvalidSkillInput(input);
            });

            input.addEventListener('blur', function () {
                clearInvalidSkillInput(input);
            });
        }

        function createSkillRow() {
            const row = document.createElement('div');
            row.className = 'job-key-skill-row';
            row.style = 'display: grid; grid-template-columns: 1fr auto; gap: 10px; margin-top: 6px;';
            row.innerHTML = `
                <input type="text" name="key_skills[]" list="jobPostingHealthcareSkillsList" placeholder="Search healthcare skills" autocomplete="off" style="width: 100%; padding: 12px; border: 1px solid #e6e6ee; border-radius: 10px; font-size: 14px;">
                <button type="button" class="remove-key-skill-btn" style="padding: 12px 16px; border: none; border-radius: 10px; color: white; cursor: pointer; background: #dc3545;">-</button>
            `;
            attachSkillValidation(row.querySelector('input[name="key_skills[]"]'));
            return row;
        }

        jobKeySkillFields.querySelectorAll('input[name="key_skills[]"]').forEach(attachSkillValidation);

        jobKeySkillFields.addEventListener('click', function (event) {
            if (event.target.classList.contains('add-key-skill-btn')) {
                jobKeySkillFields.appendChild(createSkillRow());
                return;
            }

            if (event.target.classList.contains('remove-key-skill-btn')) {
                event.target.closest('.job-key-skill-row').remove();
            }
        });

        jobPostingForm.addEventListener('submit', function (event) {
            const skillInputs = jobKeySkillFields.querySelectorAll('input[name="key_skills[]"]');
            let firstInvalidInput = null;

            skillInputs.forEach(function (input) {
                if (!clearInvalidSkillInput(input) && !firstInvalidInput) {
                    firstInvalidInput = input;
                }
            });

            if (firstInvalidInput) {
                event.preventDefault();
                firstInvalidInput.focus();
            }
        });
    });
</script>
@endsection
