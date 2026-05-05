@extends('layouts.dashboard')

@section('page-title', 'Job Board')

@section('content')
<div class="dashboard-content">
    <div class="dashboard-header">
        <h1>Healthcare Opportunities</h1>
    </div>

    @if (!$approved)
        <div style="background: #fff3cd; color: #856404; padding: 20px; border-radius: 8px; border: 1px solid #ffeaa7; text-align: center;">
            <h3 style="margin: 0 0 10px 0;">Account Pending Approval</h3>
            <p style="margin: 0;">Your account has not yet been approved by an administrator. Please wait for admin approval before viewing job postings.</p>
        </div>
    @else
        <div class="search-section" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 30px;">
            <div class="search-bar" style="display: flex; gap: 10px; align-items: center;">
                <input type="text" id="searchInput" placeholder="Search by job title, facility, location..." style="flex: 1; padding: 12px 16px; border: 1px solid #e6e6ee; border-radius: 8px; font-size: 14px; font-family: inherit;">
                <button onclick="searchJobs()" style="padding: 12px 24px; background: var(--accent); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.3s ease;">Search</button>
            </div>
        </div>

        <div class="jobs-list" id="jobsList" style="display: grid; gap: 20px;">
            <!-- Job Posts will be inserted here -->
        </div>
    @endif
</div>

<style>
    .job-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        display: flex;
        flex-direction: column;
    }

    .job-card:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }

    .job-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
        gap: 15px;
    }

    .job-card-title-section {
        flex: 1;
    }

    .job-card-title {
        color: var(--accent);
        font-size: 18px;
        margin: 0 0 8px 0;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: block;
    }

    .job-card-title:hover {
        text-decoration: underline;
    }

    .job-card-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    .job-card-meta-row {
        display: flex;
        align-items: center;
        gap: 20px;
        font-size: 13px;
        color: #999;
        margin-bottom: 12px;
    }

    .job-card-client {
        color: #666;
        font-size: 13px;
        margin: 0;
        font-weight: 500;
    }

    .job-card-date {
        color: #999;
        font-size: 12px;
        font-weight: 500;
    }

    .job-card-salary {
        background: linear-gradient(135deg, var(--accent), #7c4dff);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 12px;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .job-card-description {
        color: #555;
        font-size: 14px;
        line-height: 1.5;
        margin: 12px 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex: 1;
    }

    .job-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #e6e6ee;
    }

    .job-card-date {
        color: #999;
        font-size: 12px;
        font-weight: 500;
    }

    .job-card-actions {
        display: flex;
        gap: 8px;
    }

    .view-details-btn {
        padding: 8px 14px;
        font-size: 12px;
        background: transparent;
        color: var(--accent);
        border: 1px solid var(--accent);
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .view-details-btn:hover {
        background: var(--accent);
        color: white;
    }

    .apply-btn {
        padding: 8px 16px;
        font-size: 12px;
        background: var(--accent);
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .apply-btn:hover:not(:disabled) {
        background: #5a3fa3;
        transform: translateY(-1px);
    }

    .apply-btn:disabled {
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

    .success-notification::before {
        content: '✓';
        font-size: 24px;
        font-weight: bold;
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
        .job-card {
            padding: 16px;
        }

        .job-card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .job-card-meta {
            flex-direction: column;
            align-items: flex-start;
        }

        .job-card-footer {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .job-card-actions {
            width: 100%;
        }

        .apply-btn {
            width: 100%;
            text-align: center;
        }

        .view-details-btn {
            width: 100%;
            text-align: center;
        }
    }
</style>

<script>
    const jobPosts = @json($jobPostsData ?? []);

    function renderJobs(jobs = jobPosts) {
        const jobsList = document.getElementById('jobsList');
        jobsList.innerHTML = '';

        if (!jobs || jobs.length === 0) {
            jobsList.innerHTML = '<div style="text-align: center; color: #666; padding: 40px; background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">No jobs found matching your search.</div>';
            return;
        }

        jobs.forEach(job => {
            const jobCard = document.createElement('div');
            jobCard.className = 'job-card';
            
            // Truncate description to 200 chars
            const truncatedDesc = job.description.length > 200 
                ? job.description.substring(0, 200) + '...' 
                : job.description;

            jobCard.innerHTML = `
                <div class="job-card-header">
                    <div class="job-card-title-section">
                        <a href="/healthcare-jobs-details/${job.id}" class="job-card-title">${job.title}</a>
                        <p class="job-card-client">${job.facility}</p>
                    </div>
                    <div class="job-card-salary">${job.salary || 'N/A'}</div>
                </div>

                <div class="job-card-description">${truncatedDesc}</div>

                <div class="job-card-footer">
                    <div class="job-card-date">Posted ${job.posted}</div>
                    <div class="job-card-actions">
                        <button 
                            class="view-details-btn"
                            onclick="window.location.href='/healthcare-jobs-details/${job.id}'">
                            View Details
                        </button>
                        <button
                            id="apply-btn-${job.id}"
                            class="apply-btn"
                            ${job.applied ? 'disabled' : ''}
                            onclick="applyForJob(${job.id}, '${job.title.replace(/'/g, "\\'")}', this)">
                            ${job.applied ? 'Applied' : 'Apply Now'}
                        </button>
                    </div>
                </div>
            `;
            jobsList.appendChild(jobCard);
        });
    }

    let currentApplyJobId = null;
    let currentApplyButton = null;

    function applyForJob(jobId, jobTitle, btn) {
        if (!btn) return;
        currentApplyJobId = jobId;
        currentApplyButton = btn;

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
        if (!currentApplyJobId || !currentApplyButton) return;

        const details = document.getElementById('applicationDetails').value.trim();
        const expectedSalary = document.getElementById('expectedSalary').value.trim();
        const attachmentInput = document.getElementById('attachment');
        const attachmentFile = attachmentInput.files && attachmentInput.files[0] ? attachmentInput.files[0] : null;

        if (attachmentFile && attachmentFile.size > 5 * 1024 * 1024) {
            alert('Attachment must be 5MB or smaller.');
            return;
        }

        currentApplyButton.disabled = true;
        currentApplyButton.textContent = 'Applying...';

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
                currentApplyButton.textContent = 'Applied';
                currentApplyButton.disabled = true;
                closeApplyModal();
                showSuccessNotification('Application submitted successfully!');
            } else {
                currentApplyButton.textContent = 'Apply Now';
                currentApplyButton.disabled = false;
                alert(data.message || 'Unable to apply for this job.');
            }
        })
        .catch(() => {
            currentApplyButton.textContent = 'Apply Now';
            currentApplyButton.disabled = false;
            alert('Unable to submit application. Please try again.');
        });
    }

    function searchJobs() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const filtered = jobPosts.filter(job =>
            (job.title || '').toLowerCase().includes(searchInput) ||
            (job.facility || '').toLowerCase().includes(searchInput) ||
            (job.location || '').toLowerCase().includes(searchInput) ||
            (job.specialty || '').toLowerCase().includes(searchInput)
        );

        renderJobs(filtered);
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

    renderJobs();
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
