@php
    $workerDisplayName = $workerUser->fullname ?: ($workerUser->email ?? 'Worker');
@endphp

<div style="display: grid; gap: 24px;">
    <!-- Profile Header -->
    <div style="display: grid; grid-template-columns: auto 1fr; gap: 32px; align-items: center;">
        <div style="width: 132px; height: 132px; border-radius: 50%; background: #6b46c1; display: grid; place-items: center; color: white; font-size: 52px; font-weight: 700; overflow: hidden;">
            @if(optional($worker)->profile_photo)
                <img src="{{ asset('storage/' . $worker->profile_photo) }}" alt="{{ $workerDisplayName }}" style="width: 100%; height: 100%; object-fit: cover;">
            @else
                {{ strtoupper(substr($workerDisplayName, 0, 1)) }}
            @endif
        </div>
        <div>
            <h2 style="margin: 0 0 8px 0; font-size: 34px; color: #111827;">{{ $workerDisplayName }}</h2>
            <p style="margin: 0 0 16px 0; color: #4b5563; font-size: 16px; line-height: 1.6;">
                {{ ucfirst(optional($worker)->profession ?? 'Healthcare Professional') }}@if(optional($worker)->specialization) • {{ optional($worker)->specialization }}@endif
            </p>
            <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                <span style="padding: 10px 16px; border-radius: 999px; background: #eef2ff; color: #1d4ed8; font-weight: 600; font-size: 13px;">{{ $workerUser->verified ? 'Verified' : 'Not Verified' }}</span>
                <span style="padding: 10px 16px; border-radius: 999px; background: {{ $workerUser->approved == 1 ? '#ecfdf5' : '#fef3c7' }}; color: {{ $workerUser->approved == 1 ? '#047857' : '#92400e' }}; font-weight: 600; font-size: 13px;">{{ $workerUser->approved == 1 ? 'Approved' : 'Awaiting Approval' }}</span>
            </div>
        </div>
    </div>

    <!-- Basic Information & Skills in Grid -->
    <div style="display: grid; gap: 24px; grid-template-columns: 1fr 360px;">
        <div style="display: grid; gap: 24px;">
            <!-- Overview Section -->
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

            <!-- Skills Section -->
            <div style="background: white; border-radius: 24px; padding: 32px; box-shadow: 0 25px 60px rgba(15, 23, 42, 0.08);">
                <h3 style="margin: 0 0 20px 0; color: #111827; font-size: 20px;">Skills</h3>
                <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                    @php $skillItems = $worker ? $worker->skills->pluck('skill')->filter()->values() : collect(); @endphp
                    @if($skillItems->isEmpty())
                        <span style="padding: 10px 16px; border-radius: 999px; background: #f3f4f6; color: #6b7280;">No skills added yet</span>
                    @endif
                    @foreach($skillItems as $skill)
                        <span style="padding: 10px 16px; border-radius: 999px; background: #eef2ff; color: #1d4ed8; font-weight: 600;">{{ $skill }}</span>
                    @endforeach
                </div>
            </div>

            <!-- Professional Summary -->
            <div style="background: white; border-radius: 24px; padding: 32px; box-shadow: 0 25px 60px rgba(15, 23, 42, 0.08);">
                <h3 style="margin: 0 0 18px 0; color: #111827; font-size: 20px;">Professional Summary</h3>
                <p style="margin: 0; color: #4b5563; line-height: 1.8;">{{ optional($worker)->credentials ?: 'No profile description provided.' }}</p>
            </div>

            <!-- Work Details -->
            <div style="background: white; border-radius: 24px; padding: 32px; box-shadow: 0 25px 60px rgba(15, 23, 42, 0.08);">
                <h4 style="margin: 0 0 18px 0; color: #111827; font-size: 18px;">Work Details</h4>
                <div style="display: grid; gap: 16px;">
                    <div style="display: flex; justify-content: space-between; gap: 16px;">
                        <span style="color: #6b7280;">Profession</span>
                        <strong style="color: #111827;">{{ optional($worker)->profession ?: 'Not set' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; gap: 16px;">
                        <span style="color: #6b7280;">Specialization</span>
                        <strong style="color: #111827;">{{ optional($worker)->specialization ?: 'Not set' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; gap: 16px;">
                        <span style="color: #6b7280;">License Number</span>
                        <strong style="color: #111827;">{{ optional($worker)->license_number ?: 'Not set' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; gap: 16px;">
                        <span style="color: #6b7280;">Years of Experience</span>
                        <strong style="color: #111827;">{{ optional($worker)->experience_years ?: 'Not set' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Facility & Location -->
            <div style="background: white; border-radius: 24px; padding: 32px; box-shadow: 0 25px 60px rgba(15, 23, 42, 0.08);">
                <h4 style="margin: 0 0 18px 0; color: #111827; font-size: 18px;">Facility & Location</h4>
                <div style="display: grid; gap: 16px;">
                    <div style="display: flex; justify-content: space-between; gap: 16px;">
                        <span style="color: #6b7280;">Facility Name</span>
                        <strong style="color: #111827;">{{ optional($worker)->facility_name ?: 'Not set' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; gap: 16px;">
                        <span style="color: #6b7280;">Facility Address</span>
                        <strong style="color: #111827;">{{ optional($worker)->facility_address ?: 'Not set' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; gap: 16px;">
                        <span style="color: #6b7280;">Location</span>
                        <strong style="color: #111827;">{{ optional($worker)->location ?: 'Not set' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Employment History -->
            @if($worker && $worker->employmentHistory->isNotEmpty())
            <div style="background: white; border-radius: 24px; padding: 32px; box-shadow: 0 25px 60px rgba(15, 23, 42, 0.08);">
                <h4 style="margin: 0 0 18px 0; color: #111827; font-size: 18px;">Employment History</h4>
                <div style="display: grid; gap: 18px;">
                    @foreach($worker->employmentHistory as $employment)
                    <div style="border-left: 4px solid #6b46c1; padding-left: 18px;">
                        <h5 style="margin: 0 0 6px 0; color: #111827; font-weight: 700;">{{ $employment->job_position }}</h5>
                        <p style="margin: 0 0 6px 0; color: #6b7280; font-size: 14px;">{{ $employment->company_name }}</p>
                        <p style="margin: 0 0 6px 0; color: #4b5563; font-size: 14px; line-height: 1.6;">{{ $employment->summary }}</p>
                        <p style="margin: 0; color: #6b7280; font-size: 13px;">
                            {{ $employment->year_started }}
                            @if($employment->year_ended)
                                - {{ $employment->year_ended }}
                            @else
                                - Present
                            @endif
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- NDIS Requirements Checklist -->
            @if($ndisRequirements->isNotEmpty())
            <div style="background: white; border-radius: 24px; padding: 32px; box-shadow: 0 25px 60px rgba(15, 23, 42, 0.08);">
                <h4 style="margin: 0 0 18px 0; color: #111827; font-size: 18px;">NDIS Requirements Checklist</h4>
                <div style="display: grid; gap: 14px;">
                    @foreach($ndisRequirements as $requirement)
                        @php
                            $completed = $completedRequirements->has($requirement->id) && $completedRequirements->get($requirement->id)->document_link;
                        @endphp
                        <div style="display: flex; gap: 12px; align-items: flex-start; padding: 14px; background: {{ $completed ? '#ecfdf5' : '#f8fafc' }}; border-radius: 14px; border: 1px solid {{ $completed ? '#d1fae5' : '#e5e7eb' }};">
                            <div style="width: 24px; height: 24px; border-radius: 50%; background: {{ $completed ? '#10b981' : '#d1d5db' }}; display: grid; place-items: center; flex-shrink: 0;">
                                @if($completed)
                                    <span style="color: white; font-weight: 700; font-size: 14px;">✓</span>
                                @else
                                    <span style="color: #6b7280; font-weight: 700; font-size: 14px;">•</span>
                                @endif
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; justify-content: space-between; gap: 12px; align-items: flex-start;">
                                    <div>
                                        <p style="margin: 0 0 6px 0; color: #111827; font-weight: 600;">{{ $requirement->requirements }}</p>
                                        @if($completed && $completedRequirements->get($requirement->id)->document_link)
                                            <a href="{{ $completedRequirements->get($requirement->id)->document_link }}" target="_blank" rel="noopener" style="color: #6b46c1; font-size: 13px; text-decoration: none; font-weight: 600;">View Document →</a>
                                        @else
                                            <span style="color: #9ca3af; font-size: 13px;">Not submitted</span>
                                        @endif
                                    </div>
                                    <span style="padding: 4px 10px; border-radius: 999px; background: {{ $completed ? '#d1fae5' : '#fef3c7' }}; color: {{ $completed ? '#047857' : '#92400e' }}; font-weight: 600; font-size: 12px; white-space: nowrap;">{{ $completed ? 'Submitted' : 'Pending' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar: Basic Information -->
        <aside style="display: grid; gap: 24px; height: fit-content;">
            <div style="background: white; border-radius: 24px; padding: 32px; box-shadow: 0 25px 60px rgba(15, 23, 42, 0.08);">
                <h3 style="margin: 0 0 20px 0; color: #111827; font-size: 20px;">Basic Information</h3>
                <dl style="display: grid; gap: 14px;">
                    <div style="display: grid; gap: 6px;">
                        <span style="color: #6b7280; font-size: 13px;">Full Name</span>
                        <strong style="color: #111827;">{{ $workerDisplayName }}</strong>
                    </div>
                    <div style="display: grid; gap: 6px;">
                        <span style="color: #6b7280; font-size: 13px;">Email</span>
                        <strong style="color: #111827; word-break: break-all;">{{ $workerUser->email }}</strong>
                    </div>
                    <div style="display: grid; gap: 6px;">
                        <span style="color: #6b7280; font-size: 13px;">Phone</span>
                        <strong style="color: #111827;">{{ $workerUser->phone ?: 'Not set' }}</strong>
                    </div>
                    <div style="display: grid; gap: 6px;">
                        <span style="color: #6b7280; font-size: 13px;">Status</span>
                        <strong style="color: #111827;">{{ $workerUser->approved == 1 ? 'Approved' : 'Pending Approval' }}</strong>
                    </div>
                </dl>
            </div>
        </aside>
    </div>
</div>
