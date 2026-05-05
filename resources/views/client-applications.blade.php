@extends('layouts.dashboard')

@section('page-title', 'Applications')

@php $routePrefix = session('user')['accounttype'] ?? 'client'; @endphp

@section('content')
<div class="dashboard-content">
    <div class="dashboard-header">
        <h1>Job Applications</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="applications-container">
        <!-- Filters -->
        <div class="filter-section" style="gap: 12px; margin-bottom: 20px; align-items: center;">
            <div class="filter-group">
                <label for="job-filter">Filter by Job:</label>
                <select id="job-filter" class="filter-select" onchange="filterApplications()" style="width: 220px;">
                    <option value="">All Jobs</option>
                    @if(isset($jobPostings) && count($jobPostings) > 0)
                        @foreach($jobPostings as $job)
                            <option value="{{ $job->id }}">{{ $job->title }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="filter-group">
                <label for="status-filter">Filter by Status:</label>
                <select id="status-filter" class="filter-select" onchange="filterApplications()" style="width: 180px;">
                    <option value="">All Status</option>
                    <option value="pending">Pending Interview</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="completed">Completed</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="filter-group">
                <button class="btn btn-primary" type="button" onclick="openRankingPopup()">View Ranking</button>
            </div>
        </div>

        <!-- Applications Table -->
        <div class="table-wrapper">
            @if($applications->count() > 0)
                <table class="applications-table">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Applicant Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Applied On</th>
                            <th>Match %</th>
                            <th>Expected Salary</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $application)
                            <tr class="table-row" data-job-id="{{ $application->job_posting_id }}" data-status="{{ $application->interview_status }}">
                                <td>{{ $application->jobPosting->title ?? 'N/A' }}</td>
                                <td>{{ $application->applicant?->fullname ?: ($application->applicant?->email ?? 'N/A') }}</td>
                                <td><a href="mailto:{{ $application->applicant?->email ?? '' }}">{{ $application->applicant?->email ?? 'N/A' }}</a></td>
                                <td><a href="tel:{{ $application->applicant?->phone ?? '' }}">{{ $application->applicant?->phone ?? 'N/A' }}</a></td>
                                <td>{{ $application->created_at->format('M d, Y') }}</td>
                                <td>
                                    <span class="match-badge">{{ $application->match_percentage ?? 0 }}%</span>
                                </td>
                                <td>{{ $application->expected_salary ? '$' . number_format($application->expected_salary, 2) : 'N/A' }}</td>
                                <td>
                                    @if($application->interview_status == 'pending' || !$application->interview_status)
                                        <span class="status-badge status-pending">Pending</span>
                                    @elseif($application->interview_status == 'scheduled')
                                        <span class="status-badge status-scheduled">Scheduled</span>
                                    @elseif($application->interview_status == 'completed')
                                        <span class="status-badge status-completed">Completed</span>
                                    @elseif($application->interview_status == 'rejected')
                                        <span class="status-badge status-rejected">Rejected</span>
                                    @else
                                        <span class="status-badge status-pending">{{ ucfirst($application->interview_status) }}</span>
                                    @endif
                                </td>
                                <td class="actions-cell">
                                    @if($application->interview_status !== 'completed')
                                        <button class="btn-action" onclick="openDetailsModal({{ $application->id }})" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if(!$application->interview_status || $application->interview_status == 'pending')
                                            <button class="btn-action btn-primary" onclick="openScheduleModal({{ $application->id }})" title="Schedule Interview">
                                                <i class="fas fa-calendar"></i>
                                            </button>
                                        @endif
                                        @if($application->interview_status == 'scheduled')
                                            <button class="btn-action btn-warning" onclick="openRescheduleModal({{ $application->id }})" title="Reschedule">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        @if($application->interview_status !== 'rejected')
                                            <button class="btn-action btn-danger" onclick="rejectApplication({{ $application->id }})" title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <h3>No Applications Yet</h3>
                    <p>You don't have any applications. Post a job to start receiving applications.</p>
                    <a href="/client/job-postings/create" class="btn btn-primary">Post New Job</a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h2>Application Details</h2>
            <button class="modal-close" onclick="closeDetailsModal()">&times;</button>
        </div>
        <div class="modal-body" id="modalBody">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

<!-- Rankings Modal -->
<div id="rankingModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <div>
                <h2>Job Ranking by Fit</h2>
            </div>
            <button class="modal-close" onclick="closeRankingPopup()">&times;</button>
        </div>
        <div class="modal-body" style="flex-direction: column; padding: 24px; overflow-y: auto; gap: 20px;">
            <div id="rankingSummary" class="ranking-summary">Loading ranking summary...</div>
            <div class="panel-section" style="padding-bottom: 20px; border-bottom: 1px solid #e9ecef;">
                <label class="modal-label" for="rankingJobSelect">Select Job</label>
                <select id="rankingJobSelect" class="filter-select" onchange="renderRankingTable(this.value)" style="width: 100%; max-width: 420px; margin-top: 8px;"></select>
            </div>
            <div id="rankingContent" style="width: 100%;"></div>
        </div>
    </div>
</div>

<style>
    .applications-container {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }

    .filter-section {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-group label {
        font-weight: 600;
        white-space: nowrap;
        color: #333;
        margin: 0;
    }

    .filter-select {
        padding: 8px 12px;
        border: 1px solid #e6e6ee;
        border-radius: 8px;
        font-size: 14px;
        min-width: 150px;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .applications-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .applications-table thead {
        background: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
    }

    .applications-table th {
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: #333;
        white-space: nowrap;
    }

    .applications-table tbody tr {
        border-bottom: 1px solid #e9ecef;
        transition: background 0.2s ease;
    }

    .applications-table tbody tr:hover {
        background: #f8f9fa;
    }

    .applications-table td {
        padding: 12px;
        vertical-align: middle;
    }

    .applications-table a {
        color: var(--accent);
        text-decoration: none;
    }

    .applications-table a:hover {
        text-decoration: underline;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .match-badge {
        display: inline-block;
        min-width: 60px;
        text-align: center;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        color: #1f2937;
        background: #e0f2fe;
        border: 1px solid #bae6fd;
        white-space: nowrap;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-scheduled {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-completed {
        background: #d4edda;
        color: #155724;
    }

    .status-rejected {
        background: #f8d7da;
        color: #721c24;
    }

    .actions-cell {
        display: flex;
        gap: 8px;
    }

    .btn-action {
        background: #6c757d;
        color: white;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .btn-action:hover {
        opacity: 0.8;
        transform: scale(1.05);
    }

    .btn-action.btn-primary {
        background: var(--accent);
    }

    .btn-action.btn-warning {
        background: #ffc107;
        color: #333;
    }

    .btn-action.btn-danger {
        background: #dc3545;
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        align-items: center;
        justify-content: center;
        z-index: 2000;
        padding: 20px;
    }

    .modal.show {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 0;
        box-shadow: none;
        width: 100vw;
        height: 100vh;
        max-width: 100vw;
        max-height: 100vh;
        overflow: hidden;
        animation: slideUp 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .modal-lg {
        width: 100%;
        max-width: 100%;
        height: 100%;
    }

    .modal-body {
        padding: 0;
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 0;
        width: 100%;
        height: calc(100% - 74px);
        overflow: hidden;
    }

    .modal-panel {
        padding: 24px;
        overflow-y: auto;
        border-right: 1px solid #e9ecef;
        background: #f8f9fb;
    }

    #rankingModal .modal-content {
        width: min(100%, 860px);
        max-height: 90vh;
        border-radius: 16px;
    }

    #rankingModal .modal-body {
        display: flex;
        flex-direction: column;
        padding: 24px;
        width: 100%;
        max-height: calc(90vh - 96px);
        overflow-y: auto;
    }

    #rankingContent {
        width: 100%;
    }

    .ranking-summary {
        margin-top: 10px;
        color: #4b5563;
        font-size: 13px;
    }

    .ranking-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 16px;
    }

    .ranking-table th,
    .ranking-table td {
        border: 1px solid #e9ecef;
        padding: 12px 14px;
        text-align: left;
        font-size: 14px;
    }

    .ranking-table th {
        background: #f3f4f6;
        color: #333;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .ranking-row:nth-child(odd) {
        background: #fafbfc;
    }

    .ranking-empty {
        color: #555;
        padding: 20px 0;
    }


    .modal-panel:last-child {
        border-right: none;
        background: #fff;
    }

    .panel-title {
        font-weight: 700;
        font-size: 16px;
        margin-bottom: 14px;
    }

    .panel-section {
        margin-bottom: 16px;
    }

    .panel-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .panel-label {
        color: #666;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .panel-value {
        color: #333;
        font-size: 13px;
        font-weight: 500;
    }

    .modal-actions {
        display: flex;
        gap: 10px;
        padding: 16px;
        border-top: 1px solid #e9ecef;
        margin: 16px;
        flex-wrap: wrap;
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: white;
        height: 54px;
    }

    .modal-label {
        font-size: 14px;
        font-weight: 600;
        color: #555;
    }

    .modal-close {
        background: transparent;
        border: none;
        font-size: 28px;
        cursor: pointer;
        color: #666;
        transition: color 0.2s;
    }

    .modal-close:hover {
        color: #000;
    }

    @keyframes slideUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 24px;
        border-bottom: 1px solid #e9ecef;
    }

    .modal-header h2 {
        margin: 0;
        font-size: 20px;
        color: #333;
    }

    .modal-close {
        background: transparent;
        border: none;
        font-size: 28px;
        cursor: pointer;
        color: #666;
        transition: color 0.2s;
    }

    .modal-close:hover {
        color: #333;
    }

    .detail-group {
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f0f0f0;
    }

    .detail-group:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .detail-label {
        font-weight: 600;
        color: #666;
        font-size: 12px;
        text-transform: uppercase;
        margin-bottom: 6px;
        display: block;
    }

    .detail-value {
        font-size: 14px;
        color: #333;
    }

    .download-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--accent);
        text-decoration: none;
        padding: 8px 12px;
        border: 1px solid var(--accent);
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .download-link:hover {
        background: var(--accent);
        color: white;
    }

    .pdf-viewer {
        width: 100%;
        min-height: 320px;
        max-height: 420px;
        border: 1px solid #dfe2e6;
        border-radius: 8px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .modal-actions {
        display: flex;
        gap: 10px;
        padding-top: 20px;
        border-top: 1px solid #e9ecef;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 10px 16px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: var(--accent);
        color: white;
    }

    .btn-primary:hover {
        opacity: 0.9;
    }

    .btn-warning {
        background: #ffc107;
        color: #333;
    }

    .btn-warning:hover {
        background: #e0a800;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-icon {
        font-size: 48px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .empty-state h3 {
        font-size: 20px;
        color: #333;
        margin: 0 0 10px 0;
    }

    .empty-state p {
        color: #666;
        margin: 0 0 20px 0;
    }

    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    @media (max-width: 768px) {
        .filter-section {
            flex-direction: column;
        }

        .filter-select {
            width: 100%;
        }

        .applications-table {
            font-size: 12px;
        }

        .applications-table th,
        .applications-table td {
            padding: 8px;
        }

        .modal-lg {
            max-width: 100%;
        }

        .detail-grid {
            grid-template-columns: 1fr;
        }

        .modal-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<script>
    const routePrefix = '{{ $routePrefix }}';
    const applicationsData = @json($applications);
    const rankingJobs = @json($jobPostings ?? []);
    const jobRankings = buildRankingIndex();

    function buildRankingIndex() {
        const index = {};

        rankingJobs.forEach(job => {
            index[job.id] = {
                job,
                applications: []
            };
        });

        applicationsData.forEach(application => {
            const jobId = application.job_posting_id;
            if (!index[jobId]) {
                index[jobId] = {
                    job: {
                        id: jobId,
                        title: application.job_posting?.title || 'Unknown Job'
                    },
                    applications: []
                };
            }
            index[jobId].applications.push(application);
        });

        Object.values(index).forEach(group => {
            group.applications.sort((a, b) => {
                const aScore = parseFloat(a.metric_score ?? 0) || 0;
                const bScore = parseFloat(b.metric_score ?? 0) || 0;
                return bScore - aScore;
            });
        });

        return index;
    }

    function openRankingPopup() {
        const select = document.getElementById('rankingJobSelect');
        select.innerHTML = '<option value="">All Jobs</option>' + rankingJobs.map(job => `
            <option value="${job.id}">${job.title}</option>
        `).join('');

        if (rankingJobs.length > 0) {
            select.value = rankingJobs[0].id;
        }

        renderRankingTable(select.value);
        document.getElementById('rankingModal').classList.add('show');
    }

    function closeRankingPopup() {
        document.getElementById('rankingModal').classList.remove('show');
    }

    function renderRankingTable(jobId) {
        const content = document.getElementById('rankingContent');
        const summary = document.getElementById('rankingSummary');

        if (!jobId) {
            const groups = Object.values(jobRankings);
            const totalJobs = groups.length;
            const totalApplications = groups.reduce((sum, group) => sum + (group.applications?.length || 0), 0);

            if (groups.length === 0) {
                summary.textContent = 'No job postings are available for ranking.';
                content.innerHTML = '<div class="ranking-empty">No job postings are available.</div>';
                return;
            }

            summary.textContent = `${totalApplications} applicant${totalApplications === 1 ? '' : 's'} across ${totalJobs} job${totalJobs === 1 ? '' : 's'}.`;
            content.innerHTML = groups.map(group => buildJobRankingSection(group)).join('');
            return;
        }

        const group = jobRankings[jobId];
        if (!group) {
            summary.textContent = 'No ranking data found for this job.';
            content.innerHTML = '<div class="ranking-empty">No ranking data found for this job.</div>';
            return;
        }

        const applicantCount = group.applications?.length || 0;
        summary.textContent = `${applicantCount} applicant${applicantCount === 1 ? '' : 's'} ranked for "${group.job.title || 'Selected Job'}".`;
        content.innerHTML = buildJobRankingSection(group);
    }

    function buildJobRankingSection(group) {
        const applications = group.applications || [];
        if (!applications.length) {
            return `
                <div class="panel-section">
                    <div class="panel-title">${group.job.title || 'Untitled Job'} (0 applicants)</div>
                    <div class="ranking-empty">No applicants have submitted applications for this job yet.</div>
                </div>
            `;
        }

        return `
            <div class="panel-section" style="margin-bottom: 32px;">
                <div class="panel-title">${group.job.title || 'Untitled Job'} — Ranked Applicants (${applications.length})</div>
                <table class="ranking-table">
                    <thead>
                        <tr>
                            <th style="width: 64px;">Rank</th>
                            <th>Applicant</th>
                            <th>CV</th>
                            <th>Metric Score</th>
                            <th>Status</th>
                            <th>Applied On</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${applications.map((application, index) => buildRankingRow(application, index + 1)).join('')}
                    </tbody>
                </table>
            </div>
        `;
    }

    function buildRankingRow(application, rank) {
        const applicant = application.applicant || {};
        const name = applicant.fullname || [applicant.first_name, applicant.last_name].filter(Boolean).join(' ') || applicant.email || 'Unnamed Applicant';
        const score = parseFloat(application.metric_score ?? 0).toFixed(0);
        const status = application.interview_status ? application.interview_status.charAt(0).toUpperCase() + application.interview_status.slice(1) : 'Pending';
        const appliedOn = application.created_at ? new Date(application.created_at).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'}) : 'N/A';
        const cvLink = application.attachments ? `<a href="/${routePrefix}/applications/${application.id}/download" target="_blank" rel="noopener">View CV</a>` : 'No CV';

        return `
            <tr class="ranking-row">
                <td>${rank}</td>
                <td>${name}</td>
                <td>${cvLink}</td>
                <td>${score}%</td>
                <td>${status}</td>
                <td>${appliedOn}</td>
            </tr>
        `;
    }

    function filterApplications() {
        const jobFilter = document.getElementById('job-filter').value;
        const statusFilter = document.getElementById('status-filter').value;
        const rows = document.querySelectorAll('.table-row');

        rows.forEach(row => {
            let show = true;

            if (jobFilter && row.dataset.jobId !== jobFilter) {
                show = false;
            }

            if (statusFilter && row.dataset.status !== statusFilter) {
                show = false;
            }

            row.style.display = show ? 'table-row' : 'none';
        });
    }

    function openDetailsModal(applicationId) {
        const application = applicationsData.find(app => app.id === applicationId);
        if (!application) {
            alert('Application not found');
            return;
        }

        const worker = application.applicant || {};
        const workerName = worker.fullname || [worker.first_name, worker.last_name].filter(Boolean).join(' ') || worker.email || 'N/A';
        const workerProfile = worker.healthcare_worker || {};
        const workerSkills = workerProfile.skills || [];
        const workerEmploymentHistory = workerProfile.employment_history || workerProfile.employmentHistory || [];
        const workerNdisRequirements = workerProfile.ndis_requirements_completed || workerProfile.ndisRequirementsCompleted || [];
        const statusBadge = getStatusBadge(application.interview_status);

        const modalBody = document.getElementById('modalBody');

        modalBody.innerHTML = `
            <div class="modal-panel">
                <div class="panel-title">Worker Information</div>

                <div class="panel-section">
                    <div class="panel-row"><span class="panel-label">Name</span><span class="panel-value">${workerName}</span></div>
                    <div class="panel-row"><span class="panel-label">Email</span><span class="panel-value">${worker.email || 'N/A'}</span></div>
                    <div class="panel-row"><span class="panel-label">Phone</span><span class="panel-value">${worker.phone || 'N/A'}</span></div>
                    <div class="panel-row"><span class="panel-label">Account Type</span><span class="panel-value">${worker.accounttype || 'N/A'}</span></div>
                </div>

                <div class="panel-title">Professional Profile</div>

                <div class="panel-section">
                    <div class="panel-row"><span class="panel-label">Profession</span><span class="panel-value">${workerProfile.profession || 'N/A'}</span></div>
                    <div class="panel-row"><span class="panel-label">Specialization</span><span class="panel-value">${workerProfile.specialization || 'N/A'}</span></div>
                    <div class="panel-row"><span class="panel-label">Experience</span><span class="panel-value">${workerProfile.experience_years ? workerProfile.experience_years + ' years' : 'N/A'}</span></div>
                    <div class="panel-row"><span class="panel-label">License</span><span class="panel-value">${workerProfile.license_number || 'N/A'}</span></div>
                    <div class="panel-row"><span class="panel-label">Credentials</span><span class="panel-value">${workerProfile.credentials || 'N/A'}</span></div>
                </div>

                <div class="panel-title">Facility</div>

                <div class="panel-section">
                    <div class="panel-row"><span class="panel-label">Name</span><span class="panel-value">${workerProfile.facility_name || 'N/A'}</span></div>
                    <div class="panel-row"><span class="panel-label">Address</span><span class="panel-value">${workerProfile.facility_address || 'N/A'}</span></div>
                    <div class="panel-row"><span class="panel-label">Location</span><span class="panel-value">${workerProfile.location || 'N/A'}</span></div>
                </div>

                <div class="panel-title">Skills</div>
                <div class="panel-section">
                    ${workerSkills && workerSkills.length ? workerSkills.map(s => `<div class="panel-row" style="grid-template-columns: 1fr;">${s.skill}</div>`).join('') : '<div class="panel-value">No skills provided</div>'}
                </div>

                <div class="panel-title">Employment History</div>
                <div class="panel-section">
                    ${workerEmploymentHistory && workerEmploymentHistory.length ? workerEmploymentHistory.map(history => `
                        <div style="border: 1px solid #e9ecef; padding: 10px; border-radius: 6px; margin-bottom: 8px;">
                            <div class="panel-row"><span class="panel-label">Company</span><span class="panel-value">${history.company_name || history.companyName || 'N/A'}</span></div>
                            <div class="panel-row"><span class="panel-label">Position</span><span class="panel-value">${history.job_position || history.jobPosition || 'N/A'}</span></div>
                            <div class="panel-row"><span class="panel-label">Period</span><span class="panel-value">${history.year_started || history.yearStarted || 'N/A'} - ${(history.is_currently_employed || history.isCurrentlyEmployed) ? 'Present' : (history.year_ended || history.yearEnded || 'N/A')}</span></div>
                            <div class="panel-row"><span class="panel-label">Summary</span><span class="panel-value">${history.summary || 'N/A'}</span></div>
                        </div>
                    `).join('') : '<div class="panel-value">No employment history provided</div>'}
                </div>

                <div class="panel-title">NDIS Requirements</div>
                <div class="panel-section">
                    ${workerNdisRequirements && workerNdisRequirements.length ? workerNdisRequirements.map(requirement => `
                        <div class="panel-row" style="grid-template-columns: 1fr 1fr; gap: 12px; align-items: center;">
                            <span class="panel-label">${requirement.parameter?.requirements || requirement.parameter?.name || 'Requirement'}</span>
                            <span class="panel-value">${requirement.document_link ? `<a href="${requirement.document_link}" target="_blank" rel="noopener">View Document</a>` : 'Not submitted'}</span>
                        </div>
                    `).join('') : '<div class="panel-value">No NDIS requirements submitted</div>'}
                </div>
            </div>

            <div class="modal-panel">
                <div class="panel-title">Application Details</div>

                <div class="panel-section">
                    <div class="panel-row"><span class="panel-label">Job Position</span><span class="panel-value">${application.job_posting?.title || 'N/A'}</span></div>
                    <div class="panel-row"><span class="panel-label">Applied On</span><span class="panel-value">${new Date(application.created_at).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'})}</span></div>
                </div>

                <div class="panel-section">
                    <div class="panel-label">Application Details</div>
                    <div class="panel-value">${application.application_details || 'No details provided'}</div>
                </div>

                <div class="panel-section">
                    <div class="panel-row"><span class="panel-label">Expected Salary</span><span class="panel-value">${application.expected_salary ? '$' + parseFloat(application.expected_salary).toFixed(2) : 'N/A'}</span></div>
                    <div class="panel-row"><span class="panel-label">Status</span><span class="panel-value">${statusBadge}</span></div>
                </div>

                ${application.attachments ? (() => {
                    const fileExt = (application.attachments || '').split('.').pop().toLowerCase();
                    const attachmentUrl = `/${routePrefix}/applications/${applicationId}/download`;

                    if (fileExt === 'pdf') {
                        return `
                        <div class="panel-section">
                            <div class="panel-label">Resume Preview</div>
                            <iframe class="pdf-viewer" src="${attachmentUrl}?inline=1"></iframe>
                            <div style="margin-top: 10px;"> 
                                <a href="${attachmentUrl}" class="download-link">
                                    <i class="fas fa-download"></i> Download PDF
                                </a>
                            </div>
                        </div>
                    `;
                    }

                    return `
                        <div class="panel-section">
                            <a href="${attachmentUrl}" class="download-link">
                                <i class="fas fa-download"></i> Download CV
                            </a>
                        </div>
                    `;
                })() : ''}

                ${application.interview_date ? `
                    <div class="panel-section">
                        <div class="panel-label">Interview Scheduled</div>
                        <div class="panel-value">
                            ${new Date(application.interview_date).toLocaleDateString('en-US', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'})}<br>
                            ${new Date(application.interview_date).toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})}
                        </div>
                    </div>
                    ${application.interview_location ? `
                        <div class="panel-section">
                            <div class="panel-label">Location</div>
                            <div class="panel-value">${application.interview_location}</div>
                        </div>
                    ` : ''}
                ` : ''}

                <div class="modal-actions">
                    ${(!application.interview_status || application.interview_status === 'pending') ? `
                        <button class="btn btn-primary" onclick="openScheduleModal(${applicationId})"><i class="fas fa-calendar"></i> Schedule Interview</button>
                    ` : ''}
                    ${application.interview_status === 'scheduled' ? `
                        <button class="btn btn-warning" onclick="openRescheduleModal(${applicationId})"><i class="fas fa-edit"></i> Reschedule</button>
                    ` : ''}
                    ${application.interview_status !== 'rejected' ? `
                        <button class="btn btn-danger" onclick="rejectApplication(${applicationId})"><i class="fas fa-times"></i> Reject</button>
                    ` : ''}
                    <button class="btn btn-secondary" onclick="closeDetailsModal()">Close</button>
                </div>
            </div>
        `;

        document.getElementById('detailsModal').classList.add('show');
    }

    function getStatusBadge(status) {
        const badges = {
            'pending': '<span class="status-badge status-pending">Pending Interview</span>',
            'scheduled': '<span class="status-badge status-scheduled">Scheduled</span>',
            'completed': '<span class="status-badge status-completed">Completed</span>',
            'rejected': '<span class="status-badge status-rejected">Rejected</span>'
        };
        return badges[status] || '<span class="status-badge status-pending">Pending Interview</span>';
    }

    function closeDetailsModal() {
        document.getElementById('detailsModal').classList.remove('show');
    }

    function openScheduleModal(applicationId) {
        // Redirect to interview details for scheduling
        window.location.href = `/${routePrefix}/interviews/${applicationId}`;
    }

    function openRescheduleModal(applicationId) {
        // Redirect to interview details for rescheduling
        window.location.href = `/${routePrefix}/interviews/${applicationId}`;
    }

    function rejectApplication(applicationId) {
        if (confirm('Are you sure you want to reject this application? A rejection email will be sent to the applicant once you confirm.')) {
            fetch(`/${routePrefix}/interviews/reject/${applicationId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Application rejected successfully.');
                    location.reload();
                } else {
                    console.log(data);
                    alert(data.message || 'Error rejecting application');
                }
            })
            .catch(error => {
                alert('Error rejecting application: ' + error.message);
                console.log("Kim");
                console.error(error);
            });
        }
    }

    // Close modal when clicking outside
    document.getElementById('detailsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDetailsModal();
        }
    });
</script>
@endsection

