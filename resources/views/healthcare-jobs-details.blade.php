@extends('layouts.dashboard')

@section('page-title', $jobData['title'])

@section('content')
<div class="dashboard-content">
    <div class="job-details-container">
        <!-- Back Button -->
        <div style="margin-bottom: 20px;">
            <a href="/healthcare-jobs" style="color: var(--accent); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                <span>←</span> Back to Jobs
            </a>
        </div>

        <!-- Job Header -->
        <div class="job-details-header">
            <div>
                <h1 style="margin: 0 0 10px 0; color: var(--accent);">{{ $jobData['title'] }}</h1>
                <p style="margin: 0; color: #666; font-size: 15px;">{{ $jobData['client_name'] }}</p>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <div class="salary-badge">{{ $jobData['salary_range'] ?? 'Salary Not Specified' }}</div>
                <button 
                    id="apply-job-btn"
                    class="apply-job-btn"
                    {{ $jobData['applied'] ? 'disabled' : '' }}
                    onclick="openApplyModal({{ $jobData['id'] }}, '{{ $jobData['title'] }}')">
                    {{ $jobData['applied'] ? 'Already Applied' : 'Apply Now' }}
                </button>
            </div>
        </div>

        <!-- Job Meta Info -->
        <div class="job-meta-grid">
            <div class="meta-item">
                <div class="meta-label">Location</div>
                <div class="meta-value">{{ $jobData['location'] }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Employment Type</div>
                <div class="meta-value">{{ $jobData['employment_type'] }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Experience Level</div>
                <div class="meta-value">{{ $jobData['experience'] }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Specialty</div>
                <div class="meta-value">{{ $jobData['specialty'] }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Posted</div>
                <div class="meta-value">{{ $jobData['posted_date'] }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Time Ago</div>
                <div class="meta-value">{{ $jobData['posted'] }}</div>
            </div>
        </div>

        <!-- Job Description -->
        <div class="job-section">
            <h2 class="section-title">Job Description</h2>
            <div class="section-content">
                {!! nl2br(e($jobData['description'])) !!}
            </div>
        </div>

        <div class="job-section">
            <h2 class="section-title">Key Skills</h2>
            <div class="requirements-list">
                @if (count($jobData['key_skills']) > 0)
                    @foreach ($jobData['key_skills'] as $skill)
                        <div class="requirement-item">
                            <span class="requirement-check">âœ“</span>
                            <span>{{ $skill }}</span>
                        </div>
                    @endforeach
                @else
                    <p style="color: #999;">No key skills listed.</p>
                @endif
            </div>
        </div>

        <!-- Key Requirements -->
        <div class="job-section">
            <h2 class="section-title">Key Requirements</h2>
            <div class="requirements-list">
                @if (count($jobData['requirements']) > 0)
                    @foreach ($jobData['requirements'] as $requirement)
                        <div class="requirement-item">
                            <span class="requirement-check">✓</span>
                            <span>{{ $requirement }}</span>
                        </div>
                    @endforeach
                @else
                    <p style="color: #999;">No specific requirements listed.</p>
                @endif
            </div>
        </div>

        <!-- Apply Section -->
        <div class="apply-section">
            <button 
                id="apply-job-btn-footer"
                class="apply-job-btn-large"
                {{ $jobData['applied'] ? 'disabled' : '' }}
                onclick="openApplyModal({{ $jobData['id'] }}, '{{ $jobData['title'] }}')">
                {{ $jobData['applied'] ? 'Already Applied to this Job' : 'Apply Now' }}
            </button>
        </div>
    </div>
</div>

<style>
    .job-details-container {
        max-width: 900px;
        margin: 0;
    }

    .job-details-header {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 30px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }

    .job-details-header h1 {
        font-size: 28px;
    }

    .salary-badge {
        background: linear-gradient(135deg, var(--accent), #7c4dff);
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        white-space: nowrap;
    }

    .apply-job-btn {
        padding: 12px 24px;
        background: var(--accent);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .apply-job-btn:hover:not(:disabled) {
        background: #5a3fa3;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .apply-job-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .job-meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }

    .meta-item {
        display: flex;
        flex-direction: column;
    }

    .meta-label {
        font-size: 12px;
        color: #999;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .meta-value {
        font-size: 15px;
        color: #333;
        font-weight: 500;
    }

    .job-section {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        margin-bottom: 30px;
    }

    .section-title {
        color: var(--accent);
        font-size: 20px;
        margin: 0 0 20px 0;
        font-weight: 600;
    }

    .section-content {
        color: #555;
        line-height: 1.8;
        font-size: 15px;
    }

    .requirements-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .requirement-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 8px;
        font-size: 15px;
        color: #555;
    }

    .requirement-check {
        color: var(--accent);
        font-weight: 600;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .salary-info {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .salary-display {
        background: linear-gradient(135deg, var(--accent), #7c4dff);
        color: white;
        padding: 20px 40px;
        border-radius: 12px;
        font-size: 24px;
        font-weight: 600;
        text-align: center;
    }

    .apply-section {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        text-align: center;
        margin-bottom: 30px;
    }

    .apply-job-btn-large {
        padding: 14px 40px;
        background: var(--accent);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s ease;
        width: 100%;
        max-width: 300px;
    }

    .apply-job-btn-large:hover:not(:disabled) {
        background: #5a3fa3;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .apply-job-btn-large:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .success-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 16px 24px;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        z-index: 3000;
        max-width: 400px;
        animation: slideIn 0.4s ease-out;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @media (max-width: 768px) {
        .job-details-header {
            flex-direction: column;
            align-items: stretch;
            padding: 20px;
        }

        .job-details-header h1 {
            font-size: 22px;
        }

        .job-meta-grid {
            grid-template-columns: 1fr;
            padding: 20px;
        }

        .job-section {
            padding: 20px;
        }

        .salary-display {
            font-size: 20px;
            padding: 15px 30px;
        }

        .apply-section {
            padding: 20px;
        }
    }
</style>

<script>
    let currentApplyJobId = null;
    let currentApplyButton = null;

    function openApplyModal(jobId, jobTitle) {
        currentApplyJobId = jobId;
        currentApplyButton = null;

        // open modal
        document.getElementById('applyJobTitle').textContent = jobTitle;
        document.getElementById('applicationDetails').value = '';
        document.getElementById('expectedSalary').value = '';
        document.getElementById('attachment').value = '';
        document.getElementById('applyModal').style.display = 'flex';
    }

    function closeApplyModal() {
        document.getElementById('applyModal').style.display = 'none';
        currentApplyJobId = null;
        currentApplyButton = null;
    }

    function submitApplication() {
        if (!currentApplyJobId) return;

        const details = document.getElementById('applicationDetails').value.trim();
        const expectedSalary = document.getElementById('expectedSalary').value.trim();
        const attachmentInput = document.getElementById('attachment');
        const attachmentFile = attachmentInput.files && attachmentInput.files[0] ? attachmentInput.files[0] : null;

        if (attachmentFile && attachmentFile.size > 5 * 1024 * 1024) {
            alert('Attachment must be 5MB or smaller.');
            return;
        }

        const applyButtons = document.querySelectorAll('#apply-job-btn, #apply-job-btn-footer');
        applyButtons.forEach(btn => {
            btn.disabled = true;
            btn.textContent = 'Applying...';
        });

        const formData = new FormData();
        formData.append('application_details', details);
        formData.append('expected_salary', expectedSalary);
        if (attachmentFile) {
            formData.append('attachment', attachmentFile);
        }

        fetch(`/job-postings/${currentApplyJobId}/apply`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeApplyModal();
                showSuccessNotification('Application submitted successfully!');
                
                // Update buttons
                const buttons = document.querySelectorAll('#apply-job-btn, #apply-job-btn-footer');
                buttons.forEach(btn => {
                    btn.textContent = 'Applied';
                    btn.disabled = true;
                });
            } else {
                applyButtons.forEach(btn => {
                    btn.textContent = 'Apply Now';
                    btn.disabled = false;
                });
                alert(data.message || 'Unable to apply for this job.');
            }
        })
        .catch(() => {
            applyButtons.forEach(btn => {
                btn.textContent = 'Apply Now';
                btn.disabled = false;
            });
            alert('Unable to submit application. Please try again.');
        });
    }

    function showSuccessNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'success-notification';
        notification.textContent = message;
        notification.style.animation = 'slideIn 0.4s ease-out';
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.4s ease-in';
            setTimeout(() => {
                notification.remove();
            }, 400);
        }, 3500);
    }
</script>

<!-- Apply Modal -->
<div id="applyModal" style="display:none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 2000;">
    <div style="background: white; border-radius: 12px; width: 90%; max-width: 520px; padding: 26px; box-shadow: 0 20px 50px rgba(0,0,0,0.25); position: relative;">
        <button onclick="closeApplyModal()" style="position: absolute; top: 12px; right: 12px; background: transparent; border: none; font-size: 18px; cursor: pointer;">&times;</button>
        <h2 style="margin-top: 0;">Apply to <span id="applyJobTitle"></span></h2>
        <p style="margin: 8px 0 16px 0; color: #555;">Tell the client why you're a great fit. You can include qualifications, availability, or anything relevant.</p>

        <label style="display:block; margin-bottom: 10px; font-weight: 600;">Application details</label>
        <textarea id="applicationDetails" rows="8" style="width: 100%; min-height: 180px; padding: 12px; border: 1px solid #e6e6ee; border-radius: 12px; resize: vertical;"></textarea>

        <label style="display:block; margin: 16px 0 10px 0; font-weight: 600;">Expected salary</label>
        <input id="expectedSalary" type="text" placeholder="$50,000" style="width: 100%; padding: 10px; border: 1px solid #e6e6ee; border-radius: 8px;" />

        <label style="display:block; margin: 16px 0 10px 0; font-weight: 600;">Attachment (pdf/doc/xls, max 5MB)</label>
        <input id="attachment" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx" style="width: 100%; padding: 10px; border: 1px solid #e6e6ee; border-radius: 8px;" />

        <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
            <button onclick="closeApplyModal()" style="padding: 10px 18px; border: 1px solid #ccc; background: white; border-radius: 8px; cursor: pointer;">Cancel</button>
            <button onclick="submitApplication()" style="padding: 10px 18px; background: var(--accent); color: white; border: none; border-radius: 8px; cursor: pointer;">Submit application</button>
        </div>
    </div>
</div>
@endsection
