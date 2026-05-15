@extends('layouts.dashboard')

@section('page-title', 'My Profile')

@section('content')
@php
    $healthcareSkills = config('healthcare_skills.options', []);
    $profileSkillsOld = old('skills', $worker->skills->pluck('skill')->toArray() ?? ['']);
    if (empty($profileSkillsOld)) {
        $profileSkillsOld = [''];
    }
@endphp
<style>
    .ndis-requirements-panel {
        background: #f8fafc;
        border: 1px solid #eef2f7;
        border-radius: 24px;
        padding: 28px;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.06);
    }

    .ndis-requirements-header {
        margin: 0 0 22px 0;
        color: #0f2742;
        font-size: 18px;
        font-weight: 800;
    }

    .ndis-requirements-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .ndis-requirement-card {
        position: relative;
        display: grid;
        gap: 16px;
        overflow: hidden;
        min-height: 142px;
        padding: 24px 28px 22px 24px;
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 18px 34px rgba(15, 23, 42, 0.12);
    }

    .ndis-requirement-card::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 10px;
        height: 100%;
        background: var(--ndis-accent, #06344d);
    }

    .ndis-requirement-title {
        margin: 0;
        padding-right: 12px;
        color: #0f2742;
        font-size: 18px;
        font-weight: 800;
        line-height: 1.35;
    }

    .ndis-requirement-input {
        width: 100%;
        padding: 13px 14px;
        border: 1px solid #dce3ec;
        border-radius: 12px;
        background: #f8fafc;
        color: #111827;
        font-size: 13px;
    }

    .ndis-requirement-input:focus {
        outline: none;
        border-color: #6b46c1;
        box-shadow: 0 0 0 3px rgba(107, 70, 193, 0.14);
        background: #ffffff;
    }

    @media (max-width: 640px) {
        .ndis-requirements-panel {
            padding: 22px;
        }

        .ndis-requirements-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<div class="dashboard-content">
    <div class="dashboard-header">
        <h1>My Profile</h1>
    </div>

    @if(session('status'))
        <div style="background: #e7f5ff; color: #1d4ed8; padding: 16px; border-radius: 14px; border: 1px solid #bfdbfe; margin-bottom: 24px;">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #fef2f2; color: #b91c1c; padding: 16px; border-radius: 14px; border: 1px solid #fecaca; margin-bottom: 24px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: grid; gap: 24px;">
        <div style="background: white; border-radius: 24px; padding: 32px; box-shadow: 0 25px 60px rgba(15, 23, 42, 0.08);">
            <div style="display: grid; grid-template-columns: auto 1fr; gap: 32px; align-items: center;">
                <div style="width: 132px; height: 132px; border-radius: 50%; background: #6b46c1; display: grid; place-items: center; color: white; font-size: 52px; font-weight: 700; overflow: hidden;">
                    @if(optional($worker)->profile_photo)
                        <img src="{{ route('profile-photos.show', ['filename' => basename($worker->profile_photo)]) }}" alt="{{ $user->fullname }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        {{ strtoupper(substr($user->fullname, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: center;">
                        <h2 style="margin: 0; font-size: 34px; color: #111827;">{{ $user->fullname }}</h2>
                        <div style="display: inline-flex; align-items: center; gap: 10px; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 999px; background: #f8fafc;">
                            <div aria-label="Profile completion {{ $profileCompletionPercentage }}%" style="width: 44px; height: 44px; border-radius: 50%; background: conic-gradient(var(--accent) {{ $profileCompletionPercentage * 3.6 }}deg, #e5e7eb 0deg); display: grid; place-items: center;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: white; display: grid; place-items: center; color: var(--accent); font-size: 11px; font-weight: 800;">{{ $profileCompletionPercentage }}%</div>
                            </div>
                            <span style="color: #374151; font-size: 13px; font-weight: 700;">Profile</span>
                        </div>
                    </div>
                    <p style="margin: 12px 0 0 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                        {{ ucfirst(optional($worker)->profession ?? 'Healthcare Professional') }}@if(optional($worker)->specialization) • {{ optional($worker)->specialization }}@endif
                    </p>
                    <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-top: 18px;">
                        <span style="padding: 10px 16px; border-radius: 999px; background: #eef2ff; color: #1d4ed8; font-weight: 600; font-size: 13px;">{{ $user->verified ? 'Verified' : 'Not Verified' }}</span>
                        <span style="padding: 10px 16px; border-radius: 999px; background: {{ $user->approved == 1 ? '#ecfdf5' : '#fef3c7' }}; color: {{ $user->approved == 1 ? '#047857' : '#92400e' }}; font-weight: 600; font-size: 13px;">{{ $user->approved == 1 ? 'Approved' : 'Awaiting Approval' }}</span>
                    </div>
                    <button type="button" id="openProfilePhotoModal" style="margin-top: 16px; background: transparent; border: none; color: var(--accent); font-weight: 800; cursor: pointer; padding: 0; font-size: 14px;">Edit Profile Picture</button>
                </div>
            </div>
        </div>

        @if($incompleteProfileSections > 0 || $missingNdisCount > 0)
            <div style="display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
                @if($incompleteProfileSections > 0)
                    <div style="display: flex; gap: 14px; align-items: flex-start; background: #fffbeb; border: 1px solid #fde68a; color: #92400e; border-radius: 16px; padding: 18px 20px;">
                        <div style="width: 36px; height: 36px; border-radius: 50%; background: #f59e0b; color: white; display: grid; place-items: center; font-weight: 800;">!</div>
                        <div>
                            <strong style="display: block; color: #78350f; margin-bottom: 4px;">Complete your profile</strong>
                            <span style="line-height: 1.5;">Some profile sections still need details before your profile is fully complete.</span>
                        </div>
                    </div>
                @endif

                @if($missingNdisCount > 0)
                    <div style="display: flex; gap: 14px; align-items: flex-start; background: #fffbeb; border: 1px solid #fde68a; color: #92400e; border-radius: 16px; padding: 18px 20px;">
                        <div style="width: 36px; height: 36px; border-radius: 50%; background: #f59e0b; color: white; display: grid; place-items: center; font-weight: 800;">!</div>
                        <div>
                            <strong style="display: block; color: #78350f; margin-bottom: 4px;">Upload NDIS requirement links</strong>
                            <span style="line-height: 1.5;">Add Google Drive or Dropbox links for your remaining NDIS documents.</span>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <div style="display: grid; gap: 24px; grid-template-columns: 1fr 360px;">
            <div style="display: grid; gap: 24px;">
                <div style="background: white; border-radius: 24px; padding: 32px; box-shadow: 0 25px 60px rgba(15, 23, 42, 0.08);">
                    <h3 style="margin: 0 0 20px 0; color: #111827; font-size: 20px;">Overview</h3>
                    <div style="display: grid; gap: 16px;">
                        <div style="display: flex; justify-content: space-between; gap: 16px;">
                            <span style="color: #6b7280;">Facility</span>
                            <strong style="color: #111827;">{{ optional($worker)->facility_name ?: 'No facility provided' }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; gap: 16px;">
                            <span style="color: #6b7280;">Location</span>
                            <strong style="color: #111827;">{{ optional($worker)->location ?: 'Not set' }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; gap: 16px;">
                            <span style="color: #6b7280;">Experience</span>
                            <strong style="color: #111827;">{{ optional($worker)->experience_years ? optional($worker)->experience_years . ' yrs' : '0 yrs' }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; gap: 16px;">
                            <span style="color: #6b7280;">License</span>
                            <strong style="color: #111827;">{{ optional($worker)->license_number ?: 'Not set' }}</strong>
                        </div>
                    </div>
                </div>
                <div style="background: white; border-radius: 24px; padding: 32px; box-shadow: 0 25px 60px rgba(15, 23, 42, 0.08);">
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 20px;">
                        <h3 style="margin: 0; color: #111827; font-size: 20px;">Skills</h3>
                        <button type="button" id="openSkillsModal" style="background: transparent; border: none; color: var(--accent); font-weight: 800; cursor: pointer; padding: 0; font-size: 14px;">Edit Skills</button>
                    </div>
                    <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                        @php $skillItems = $worker->skills->pluck('skill')->filter()->values(); @endphp
                        @if($skillItems->isEmpty())
                            <span style="padding: 10px 16px; border-radius: 999px; background: #f3f4f6; color: #6b7280;">No skills added yet</span>
                        @endif
                        @foreach($skillItems as $skill)
                            <span style="padding: 10px 16px; border-radius: 999px; background: #eef2ff; color: #1d4ed8; font-weight: 600;">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            <aside style="display: grid; gap: 24px;">
                <div style="background: white; border-radius: 24px; padding: 32px; box-shadow: 0 25px 60px rgba(15, 23, 42, 0.08);">
                    <h3 style="margin: 0 0 20px 0; color: #111827; font-size: 20px;">Basic Information</h3>
                    <dl style="display: grid; gap: 14px;">
                        <div style="display: grid; gap: 6px;"><span style="color: #6b7280;">Full Name</span><strong>{{ $user->fullname }}</strong></div>
                        <div style="display: grid; gap: 6px;"><span style="color: #6b7280;">Email</span><strong>{{ $user->email }}</strong></div>
                        <div style="display: grid; gap: 6px;"><span style="color: #6b7280;">Phone</span><strong>{{ $user->phone ?: 'Not set' }}</strong></div>
                        <div style="display: grid; gap: 6px;"><span style="color: #6b7280;">Approval Status</span><strong>{{ $user->approved == 1 ? 'Approved' : 'Pending Approval' }}</strong></div>
                    </dl>
                </div>
            </aside>
        </div>

        <div style="display: grid; gap: 24px;">
            <div style="background: white; border-radius: 24px; padding: 32px; box-shadow: 0 25px 60px rgba(15, 23, 42, 0.08);">
                <h3 style="margin: 0 0 18px 0; color: #111827; font-size: 20px;">Professional Summary</h3>
                <p style="margin: 0; color: #4b5563; line-height: 1.8;">{{ optional($worker)->credentials ?: 'No profile description yet. Use this space to summarize your experience, qualifications, and what makes you a strong healthcare professional.' }}</p>
            </div>

            <form method="POST" action="{{ url('/healthcare-profile') }}" style="display: grid; gap: 24px;">
                @csrf

                <div style="display: grid; gap: 24px; grid-template-columns: repeat(2, minmax(0, 1fr));">
                    <div style="background: #ffffff; border-radius: 24px; padding: 28px; box-shadow: 0 20px 50px rgba(15, 23, 42, 0.06);">
                        <h4 style="margin: 0 0 18px 0; color: #111827; font-size: 18px;">Work Details</h4>
                        <div style="display: grid; gap: 18px;">
                            <label style="display: grid; gap: 8px; font-weight: 600; color: #111827;">Profession *
                                <input type="text" id="profession" name="profession" value="{{ old('profession', optional($worker)->profession) }}" required style="width: 100%; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: #f8fafc; color: #111827;">
                            </label>
                            <label style="display: grid; gap: 8px; font-weight: 600; color: #111827;">Specialization
                                <input type="text" id="specialization" name="specialization" value="{{ old('specialization', optional($worker)->specialization) }}" style="width: 100%; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: #f8fafc; color: #111827;">
                            </label>
                            <label style="display: grid; gap: 8px; font-weight: 600; color: #111827;">License Number
                                <input type="text" id="license_number" name="license_number" value="{{ old('license_number', optional($worker)->license_number) }}" style="width: 100%; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: #f8fafc; color: #111827;">
                            </label>
                            <label style="display: grid; gap: 8px; font-weight: 600; color: #111827;">Years of Experience
                                <input type="number" id="experience_years" name="experience_years" min="0" value="{{ old('experience_years', optional($worker)->experience_years) }}" style="width: 100%; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: #f8fafc; color: #111827;">
                            </label>
                        </div>
                    </div>

                    <div style="background: #ffffff; border-radius: 24px; padding: 28px; box-shadow: 0 20px 50px rgba(15, 23, 42, 0.06);">
                        <h4 style="margin: 0 0 18px 0; color: #111827; font-size: 18px;">Facility & Location</h4>
                        <div style="display: grid; gap: 18px;">
                            <label style="display: grid; gap: 8px; font-weight: 600; color: #111827;">Facility Name
                                <input type="text" id="facility_name" name="facility_name" value="{{ old('facility_name', optional($worker)->facility_name) }}" style="width: 100%; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: #f8fafc; color: #111827;">
                            </label>
                            <label style="display: grid; gap: 8px; font-weight: 600; color: #111827;">Facility Address
                                <input type="text" id="facility_address" name="facility_address" value="{{ old('facility_address', optional($worker)->facility_address) }}" style="width: 100%; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: #f8fafc; color: #111827;">
                            </label>
                            <label style="display: grid; gap: 8px; font-weight: 600; color: #111827;">Location
                                <input type="text" id="healthcareProfileLocationInput" name="location" value="{{ old('location', optional($worker)->location) }}" placeholder="Enter your location" autocomplete="off" style="width: 100%; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: #f8fafc; color: #111827;">
                            </label>
                        </div>
                    </div>
                </div>

                <div style="display: grid; gap: 24px;">
                    <div style="display: none;">
                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 20px;">
                            <h4 style="margin: 0; color: #111827; font-size: 18px;">Skills</h4>
                        </div>
                        <p style="margin: 0 0 18px 0; color: #6b7280;">Start typing to search the healthcare skills catalog, then add more as needed.</p>
                        <div id="legacyProfileSkills" style="display: grid; gap: 14px;">
                            @foreach($profileSkillsOld as $skillIndex => $skillVal)
                                <div class="profile-skill-row" style="display: grid; grid-template-columns: 1fr auto; gap: 12px; align-items: center;">
                                    <input type="text" value="{{ $skillVal }}" placeholder="Search healthcare skills" autocomplete="off" disabled style="width: 100%; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: #f8fafc; color: #111827;">
                                    <button type="button" class="{{ $skillIndex === 0 ? 'add-skill-btn' : 'remove-skill-btn' }}" style="padding: 14px 18px; border: none; background: {{ $skillIndex === 0 ? '#4338ca' : '#ef4444' }}; color: white; border-radius: 14px; cursor: pointer; font-size: 18px;">{{ $skillIndex === 0 ? '+' : '−' }}</button>
                                </div>
                            @endforeach
                        </div>
                        <datalist id="profileHealthcareSkillsList">
                            @foreach($healthcareSkills as $skillOption)
                                <option value="{{ $skillOption }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    <div style="background: #ffffff; border-radius: 24px; padding: 28px; box-shadow: 0 20px 50px rgba(15, 23, 42, 0.06);">
                        <h4 style="margin: 0 0 18px 0; color: #111827; font-size: 18px;">Professional Summary</h4>
                        <textarea id="credentials" name="credentials" rows="6" style="width: 100%; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: #f8fafc; color: #111827; resize: vertical;">{{ old('credentials', optional($worker)->credentials) }}</textarea>
                    </div>
                </div>

                <div style="background: #ffffff; border-radius: 24px; padding: 28px; box-shadow: 0 20px 50px rgba(15, 23, 42, 0.06);">
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 18px;">
                        <h4 style="margin: 0; color: #111827; font-size: 18px;">Employment History</h4>
                        <button type="button" id="addProfileEmployment" style="background: #4338ca; color: white; border: none; padding: 12px 18px; border-radius: 14px; cursor: pointer; font-weight: 600;">+ Add Job</button>
                    </div>
                    <div id="profileEmploymentHistory" style="display: grid; gap: 18px;">
                        @php $historyItems = old('employment_history', $worker->employmentHistory->toArray() ?? []); @endphp
                        @foreach($historyItems as $index => $historyItem)
                            <div class="employment-row" style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 20px; padding: 22px; display: grid; gap: 18px;">
                                <div style="display: grid; gap: 14px; grid-template-columns: repeat(2, minmax(0, 1fr));">
                                    <input type="text" name="employment_history[{{ $index }}][company_name]" placeholder="Company Name" value="{{ $historyItem['company_name'] ?? '' }}" style="width: 100%; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: white;">
                                    <input type="text" name="employment_history[{{ $index }}][job_position]" placeholder="Job Position" value="{{ $historyItem['job_position'] ?? '' }}" style="width: 100%; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: white;">
                                </div>
                                <textarea name="employment_history[{{ $index }}][summary]" placeholder="Summary" style="width: 100%; min-height: 120px; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: white;">{{ $historyItem['summary'] ?? '' }}</textarea>
                                <div style="display: grid; gap: 14px; grid-template-columns: repeat(2, minmax(0, 1fr));">
                                    <input type="text" name="employment_history[{{ $index }}][year_started]" placeholder="Year Started" value="{{ $historyItem['year_started'] ?? '' }}" style="width: 100%; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: white;">
                                    <input type="text" name="employment_history[{{ $index }}][year_ended]" placeholder="Year Ended" value="{{ $historyItem['year_ended'] ?? '' }}" style="width: 100%; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: white;">
                                </div>
                                <label style="display: flex; align-items: center; gap: 10px; color: #4b5563; font-weight: 600;"><input type="checkbox" name="employment_history[{{ $index }}][is_currently_employed]" value="1" {{ !empty($historyItem['is_currently_employed']) ? 'checked' : '' }}>Currently employed</label>
                                <button type="button" class="remove-employment-btn" style="background: #ef4444; color: white; border: none; padding: 12px 18px; border-radius: 14px; cursor: pointer; font-weight: 600; width: fit-content;">Remove</button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="ndis-requirements-panel">
                    <h4 class="ndis-requirements-header">NDIS Requirements</h4>
                    @if($ndisRequirements->isEmpty())
                        <div style="padding: 18px; border: 1px dashed #d1d5db; border-radius: 16px; color: #6b7280; background: #f9fafb;">
                            No NDIS requirements are currently configured.
                        </div>
                    @else
                        <div class="ndis-requirements-grid">
                            @foreach($ndisRequirements as $requirement)
                                @php
                                    $completedRequirement = $completedRequirements->get($requirement->id);
                                    $documentValue = old('ndis_requirements_completed.' . $requirement->id . '.document_link', optional($completedRequirement)->document_link);
                                    $accentColors = ['#06344d', '#8b1db1', '#4c1dce', '#f59e0b'];
                                    $accentColor = $accentColors[$loop->index % count($accentColors)];
                                @endphp
                                <label class="ndis-requirement-card" style="--ndis-accent: {{ $accentColor }};">
                                    <span class="ndis-requirement-title">{{ $requirement->requirements }}</span>
                                    <input class="ndis-requirement-input" type="url" name="ndis_requirements_completed[{{ $requirement->id }}][document_link]" value="{{ $documentValue }}" placeholder="Paste Google Drive or Dropbox link when completed">
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div style="text-align: right;">
                    <button type="submit" style="background: #6b46c1; color: white; border: none; padding: 16px 32px; border-radius: 16px; font-weight: 700; font-size: 16px; cursor: pointer;">Save Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="skillsModal" style="display: none; position: fixed; inset: 0; z-index: 1200; background: rgba(15, 23, 42, 0.55); padding: 24px; align-items: center; justify-content: center;">
    <div style="width: min(680px, 100%); max-height: 90vh; overflow-y: auto; background: white; border-radius: 20px; padding: 28px; box-shadow: 0 30px 80px rgba(15, 23, 42, 0.25);">
        <div style="display: flex; justify-content: space-between; gap: 16px; align-items: center; margin-bottom: 22px;">
            <h3 style="margin: 0; color: #111827; font-size: 22px;">Edit Skills</h3>
            <button type="button" id="closeSkillsModal" style="background: none; border: none; color: #6b7280; cursor: pointer; font-size: 26px; line-height: 1;">&times;</button>
        </div>

        <form method="POST" action="{{ url('/healthcare-profile/skills') }}" style="display: grid; gap: 18px;">
            @csrf
            <p style="margin: 0; color: #6b7280;">Start typing to search the healthcare skills catalog, then add more as needed.</p>
            <div id="profileSkills" style="display: grid; gap: 14px;">
                @foreach($profileSkillsOld as $skillIndex => $skillVal)
                    <div class="profile-skill-row" style="display: grid; grid-template-columns: 1fr auto; gap: 12px; align-items: center;">
                        <input type="text" name="skills[]" list="skillsHealthcareSkillsList" value="{{ $skillVal }}" placeholder="Search healthcare skills" autocomplete="off" style="width: 100%; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: #f8fafc; color: #111827;">
                        <button type="button" class="{{ $skillIndex === 0 ? 'add-skill-btn' : 'remove-skill-btn' }}" style="padding: 14px 18px; border: none; background: {{ $skillIndex === 0 ? '#4338ca' : '#ef4444' }}; color: white; border-radius: 14px; cursor: pointer; font-size: 18px;">{{ $skillIndex === 0 ? '+' : '−' }}</button>
                    </div>
                @endforeach
            </div>
            <datalist id="skillsHealthcareSkillsList">
                @foreach($healthcareSkills as $skillOption)
                    <option value="{{ $skillOption }}"></option>
                @endforeach
            </datalist>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 6px;">
                <button type="button" id="cancelSkillsModal" style="background: #f3f4f6; color: #374151; border: none; padding: 12px 18px; border-radius: 12px; cursor: pointer; font-weight: 700;">Cancel</button>
                <button type="submit" style="background: #6b46c1; color: white; border: none; padding: 12px 18px; border-radius: 12px; cursor: pointer; font-weight: 700;">Save Skills</button>
            </div>
        </form>
    </div>
</div>

<div id="profilePhotoModal" style="display: none; position: fixed; inset: 0; z-index: 1200; background: rgba(15, 23, 42, 0.55); padding: 24px; align-items: center; justify-content: center;">
    <div style="width: min(520px, 100%); background: white; border-radius: 20px; padding: 28px; box-shadow: 0 30px 80px rgba(15, 23, 42, 0.25);">
        <div style="display: flex; justify-content: space-between; gap: 16px; align-items: center; margin-bottom: 22px;">
            <h3 style="margin: 0; color: #111827; font-size: 22px;">Edit Profile Picture</h3>
            <button type="button" id="closeProfilePhotoModal" style="background: none; border: none; color: #6b7280; cursor: pointer; font-size: 26px; line-height: 1;">&times;</button>
        </div>

        <form method="POST" action="{{ url('/healthcare-profile/photo') }}" enctype="multipart/form-data" style="display: grid; gap: 18px;">
            @csrf
            <div style="display: grid; place-items: center; gap: 14px;">
                <div style="width: 112px; height: 112px; border-radius: 50%; background: #6b46c1; display: grid; place-items: center; color: white; font-size: 44px; font-weight: 700; overflow: hidden;">
                    @if(optional($worker)->profile_photo)
                        <img src="{{ route('profile-photos.show', ['filename' => basename($worker->profile_photo)]) }}" alt="{{ $user->fullname }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        {{ strtoupper(substr($user->fullname, 0, 1)) }}
                    @endif
                </div>
                <span style="color: #6b7280; font-size: 14px;">JPG, PNG, or WEBP. Maximum file size is 2 MB.</span>
            </div>

            <input type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp" style="width: 100%; padding: 12px 14px; border: 1px solid #e5e7eb; border-radius: 14px; background: #f8fafc; color: #111827;">

            @if(optional($worker)->profile_photo)
                <label style="display: flex; align-items: center; gap: 10px; color: #4b5563; font-weight: 600;">
                    <input type="checkbox" name="remove_profile_photo" value="1">
                    Remove current photo
                </label>
            @endif

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 6px;">
                <button type="button" id="cancelProfilePhotoModal" style="background: #f3f4f6; color: #374151; border: none; padding: 12px 18px; border-radius: 12px; cursor: pointer; font-weight: 700;">Cancel</button>
                <button type="submit" style="background: #6b46c1; color: white; border: none; padding: 12px 18px; border-radius: 12px; cursor: pointer; font-weight: 700;">Save Picture</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profilePhotoModal = document.getElementById('profilePhotoModal');
        const openProfilePhotoModal = document.getElementById('openProfilePhotoModal');
        const closeProfilePhotoModal = document.getElementById('closeProfilePhotoModal');
        const cancelProfilePhotoModal = document.getElementById('cancelProfilePhotoModal');
        const skillsModal = document.getElementById('skillsModal');
        const openSkillsModal = document.getElementById('openSkillsModal');
        const closeSkillsModal = document.getElementById('closeSkillsModal');
        const cancelSkillsModal = document.getElementById('cancelSkillsModal');

        function showProfilePhotoModal() {
            profilePhotoModal.style.display = 'flex';
        }

        function hideProfilePhotoModal() {
            profilePhotoModal.style.display = 'none';
        }

        if (openProfilePhotoModal) {
            openProfilePhotoModal.addEventListener('click', showProfilePhotoModal);
        }

        if (closeProfilePhotoModal) {
            closeProfilePhotoModal.addEventListener('click', hideProfilePhotoModal);
        }

        if (cancelProfilePhotoModal) {
            cancelProfilePhotoModal.addEventListener('click', hideProfilePhotoModal);
        }

        if (profilePhotoModal) {
            profilePhotoModal.addEventListener('click', function(event) {
                if (event.target === profilePhotoModal) {
                    hideProfilePhotoModal();
                }
            });
        }

        function showSkillsModal() {
            skillsModal.style.display = 'flex';
        }

        function hideSkillsModal() {
            skillsModal.style.display = 'none';
        }

        if (openSkillsModal) {
            openSkillsModal.addEventListener('click', showSkillsModal);
        }

        if (closeSkillsModal) {
            closeSkillsModal.addEventListener('click', hideSkillsModal);
        }

        if (cancelSkillsModal) {
            cancelSkillsModal.addEventListener('click', hideSkillsModal);
        }

        if (skillsModal) {
            skillsModal.addEventListener('click', function(event) {
                if (event.target === skillsModal) {
                    hideSkillsModal();
                }
            });
        }

        const profileSkills = document.getElementById('profileSkills');
        const profileForm = document.querySelector('form[action="{{ url('/healthcare-profile/skills') }}"]');
        const profileSkillsList = document.getElementById('skillsHealthcareSkillsList');
        const allowedSkills = new Set(Array.from(profileSkillsList.options).map(option => option.value.trim()));

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

        function createProfileSkillRow() {
            const row = document.createElement('div');
            row.className = 'profile-skill-row';
            row.style = 'display: grid; grid-template-columns: 1fr auto; gap: 12px; align-items: center;';
            row.innerHTML = `
                <input type="text" name="skills[]" list="skillsHealthcareSkillsList" placeholder="Search healthcare skills" autocomplete="off" style="width: 100%; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: #f8fafc; color: #111827;">
                <button type="button" class="remove-skill-btn" style="padding: 14px 18px; border: none; background: #ef4444; color: white; border-radius: 14px; cursor: pointer; font-size: 18px;">−</button>
            `;
            attachSkillValidation(row.querySelector('input[name="skills[]"]'));
            return row;
        }

        profileSkills.querySelectorAll('input[name="skills[]"]').forEach(attachSkillValidation);

        profileSkills.addEventListener('click', function(e) {
            if (e.target.classList.contains('add-skill-btn')) {
                profileSkills.appendChild(createProfileSkillRow());
                return;
            }

            if (e.target.classList.contains('remove-skill-btn')) {
                e.target.closest('.profile-skill-row').remove();
            }
        });

        profileForm.addEventListener('submit', function (event) {
            const skillInputs = profileSkills.querySelectorAll('input[name="skills[]"]');
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

        const profileEmployment = document.getElementById('profileEmploymentHistory');
        const addProfileEmployment = document.getElementById('addProfileEmployment');
        let historyCounter = profileEmployment.querySelectorAll('.employment-row').length;

        addProfileEmployment.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'employment-row';
            row.style = 'background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 20px; padding: 22px; display: grid; gap: 18px;';
            row.innerHTML = `
                <div style="display: grid; gap: 14px; grid-template-columns: repeat(2, minmax(0, 1fr));">
                    <input type="text" name="employment_history[${historyCounter}][company_name]" placeholder="Company Name" style="width: 100%; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: white;">
                    <input type="text" name="employment_history[${historyCounter}][job_position]" placeholder="Job Position" style="width: 100%; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: white;">
                </div>
                <textarea name="employment_history[${historyCounter}][summary]" placeholder="Summary" style="width: 100%; min-height: 120px; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: white;"></textarea>
                <div style="display: grid; gap: 14px; grid-template-columns: repeat(2, minmax(0, 1fr));">
                    <input type="text" name="employment_history[${historyCounter}][year_started]" placeholder="Year Started" style="width: 100%; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: white;">
                    <input type="text" name="employment_history[${historyCounter}][year_ended]" placeholder="Year Ended" style="width: 100%; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: white;">
                </div>
                <label style="display: flex; align-items: center; gap: 10px; color: #4b5563; font-weight: 600;"><input type="checkbox" name="employment_history[${historyCounter}][is_currently_employed]" value="1">Currently employed</label>
                <button type="button" class="remove-employment-btn" style="background: #ef4444; color: white; border: none; padding: 12px 18px; border-radius: 14px; cursor: pointer; font-weight: 600; width: fit-content;">Remove</button>
            `;
            profileEmployment.insertBefore(row, addProfileEmployment);
            historyCounter++;
        });

        profileEmployment.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-employment-btn')) {
                e.target.closest('.employment-row').remove();
            }
        });
    });
</script>
@endsection
