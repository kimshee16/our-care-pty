@extends('layouts.dashboard')

@section('page-title', 'Interviews')

@php $routePrefix = session('user')['accounttype'] ?? 'client'; @endphp

@section('content')
<div class="dashboard-content">
    <div class="dashboard-header">
        <h1>Interview Management</h1>
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

    <div class="interviews-container" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
        <!-- Filters -->
        <div class="filter-section" style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; align-items: center;">
            <div class="filter-group" style="display: flex; align-items: center; gap: 8px;">
                <label for="job-filter" style="font-weight: 600; white-space: nowrap; color: #333; margin: 0;">Filter by Job:</label>
                <select id="job-filter" class="filter-select" onchange="filterInterviews()" style="padding: 8px 12px; border: 1px solid #e6e6ee; border-radius: 8px; font-size: 14px; min-width: 150px; width: 220px;">
                    <option value="">All Jobs</option>
                    @if(isset($jobPostings) && count($jobPostings) > 0)
                        @foreach($jobPostings as $job)
                            <option value="{{ $job->id }}">{{ $job->title }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="filter-group" style="display: flex; align-items: center; gap: 8px;">
                <label for="status-filter" style="font-weight: 600; white-space: nowrap; color: #333; margin: 0;">Filter by Status:</label>
                <select id="status-filter" class="filter-select" onchange="filterInterviews()" style="padding: 8px 12px; border: 1px solid #e6e6ee; border-radius: 8px; font-size: 14px; min-width: 150px; width: 180px;">
                    <option value="">All Status</option>
                    <option value="pending">Pending Interview</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="completed">Completed</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>

        <!-- Interviews List -->
        <div class="interviews-list" style="overflow-x: auto;">
                        <style>
                            .interviews-list table {
                                width: 100%;
                                border-collapse: collapse;
                                font-size: 14px;
                            }
                            .interviews-list thead {
                                background: #f8f9fa;
                                border-bottom: 2px solid #e9ecef;
                            }
                            .interviews-list th {
                                padding: 12px;
                                text-align: left;
                                font-weight: 600;
                                color: #333;
                                white-space: nowrap;
                            }
                            .interviews-list tbody tr {
                                border-bottom: 1px solid #e9ecef;
                                transition: background 0.2s ease;
                            }
                            .interviews-list tbody tr:hover {
                                background: #f8f9fa;
                            }
                            .interviews-list td {
                                padding: 12px;
                                vertical-align: middle;
                            }
                            .interviews-list a {
                                color: var(--accent);
                                text-decoration: none;
                            }
                            .interviews-list a:hover {
                                text-decoration: underline;
                            }
                            .interviews-list .status-badge {
                                display: inline-block;
                                padding: 6px 12px;
                                border-radius: 20px;
                                font-size: 11px;
                                font-weight: 600;
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
                            .status-rescheduled {
                                background: #ffe082;
                                color: #7c5c00;
                            }
                            .btn-action {
                                background: #6c757d;
                                color: white;
                                border: none;
                                width: 32px;
                                height: 32px;
                                border-radius: 6px;
                                cursor: pointer;
                                display: inline-flex;
                                align-items: center;
                                justify-content: center;
                                transition: all 0.3s ease;
                                font-size: 14px;
                                margin-right: 4px;
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
                            .btn-action.btn-success {
                                background: #28a745;
                            }
                            .btn-action.btn-info {
                                background: #17a2b8;
                            }
                            .btn-action.btn-danger {
                                background: #dc3545;
                            }
                        </style>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Location</th>
                        <th>Applicant Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Applied On</th>
                        <th>Interview Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($applications as $application)
                    <tr class="table-row" data-job-id="{{ $application->job_posting_id }}" data-status="{{ $application->interview_status ?? 'pending' }}">
                        <td>{{ $application->jobPosting->title ?? 'N/A' }}</td>
                        <td>{{ $application->jobPosting->location ?? 'Location not specified' }}</td>
                        <td>{{ $application->applicant?->fullname ?? 'N/A' }}</td>
                        <td>
                            <a href="mailto:{{ $application->applicant?->email ?? '' }}">
                                {{ $application->applicant?->email ?? 'N/A' }}
                            </a>
                        </td>
                        <td>
                            <a href="tel:{{ $application->applicant?->phone ?? '' }}">
                                {{ $application->applicant?->phone ?? 'N/A' }}
                            </a>
                        </td>
                        <td>{{ $application->created_at->format('M d, Y') }}</td>
                        <td>
                            @if($application->interview_status == 'pending' || !$application->interview_status)
                                <span class="status-badge status-pending">Pending</span>
                            @elseif($application->interview_status == 'scheduled')
                                <span class="status-badge status-scheduled">Scheduled</span>
                            @elseif($application->interview_status == 'rescheduled')
                                <span class="status-badge status-rescheduled">Rescheduled</span>
                            @elseif($application->interview_status == 'completed')
                                <span class="status-badge status-completed">Completed</span>
                            @elseif($application->interview_status == 'rejected')
                                <span class="status-badge status-rejected">Rejected</span>
                            @else
                                <span class="status-badge status-pending">{{ ucfirst($application->interview_status) }}</span>
                            @endif
                        </td>
                        <td style="display: flex; gap: 8px;">
                            @if($application->interview_status !== 'completed')
                                @if(!$application->interview_status || $application->interview_status == 'pending')
                                    <button class="btn-action btn-primary" onclick="openScheduleModal({{ $application->id }})" title="Schedule Interview">
                                        <i class="fas fa-calendar"></i>
                                    </button>
                                @endif
                                @if($application->interview_status == 'scheduled' || $application->interview_status == 'rescheduled')
                                    <button class="btn-action btn-warning" onclick="openRescheduleModal({{ $application->id }})" title="Reschedule">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action btn-success" onclick="completeInterview({{ $application->id }})" title="Mark as Job Done">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                                @if($application->interview_status == 'scheduled' || $application->interview_status == 'rescheduled')
                                    <button class="btn-action btn-info" onclick="openNotesModal({{ $application->id }})" title="Add Notes">
                                        <i class="fas fa-sticky-note"></i>
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
                @empty
                    <tr>
                        <td colspan="10" class="text-center">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <h3>No Applications Yet</h3>
                                <p>You don't have any job applications. Post a job to start receiving applications.</p>
                                @php
                                    $sessionUser = session('user', []);
                                    $clientApproved = ($sessionUser['accounttype'] ?? '') === 'client' && ($sessionUser['approved'] ?? 0) == 1;
                                @endphp
                                @if($clientApproved)
                                    <a href="/client/job-postings/create" class="btn btn-primary">Post New Job</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Interview Modal -->
    <div id="createInterviewModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeCreateModal()">&times;</span>
            <h2>Create Interview</h2>
            <form id="createInterviewForm" method="POST" action="/{{ $routePrefix }}/interviews">
                @csrf

                <div class="form-group">
                    <label for="jobPostingSelect">Job Posting:</label>
                    <select id="jobPostingSelect" name="job_posting_id" required>
                        <option value="">Select a job</option>
                        @foreach($jobPostings as $job)
                            <option value="{{ $job->id }}">{{ $job->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="candidateSelect">Candidate:</label>
                    <select id="candidateSelect" name="candidate_id" required>
                        <option value="">Select a candidate</option>
                        @foreach($candidates as $candidate)
                            <option value="{{ $candidate->id }}">{{ $candidate->fullname }} ({{ $candidate->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="createInterviewDate">Interview Date & Time:</label>
                    <input type="datetime-local" id="createInterviewDate" name="interview_date" required>
                </div>

                <div class="form-group">
                    <label for="createInterviewLocation">Location (optional):</label>
                    <input type="text" id="createInterviewLocation" name="interview_location" placeholder="e.g., Conference Room A or Video Call">
                </div>

                <div class="form-group">
                    <label for="createInterviewNotes">Additional Notes (optional):</label>
                    <textarea id="createInterviewNotes" name="additional_notes" rows="4" placeholder="Add any instructions or notes for the candidate..."></textarea>
                </div>

                <div class="form-group">
                    <label for="applicationDetails">Application Details (optional):</label>
                    <textarea id="applicationDetails" name="application_details" rows="3" placeholder="Optional notes about the candidate or role..."></textarea>
                </div>

                <div class="form-group">
                    <label for="expectedSalary">Expected Salary (optional):</label>
                    <input type="number" step="0.01" id="expectedSalary" name="expected_salary" placeholder="e.g., 65000">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Create Interview</button>
                    <button type="button" class="btn btn-secondary" onclick="closeCreateModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Schedule Interview Modal -->
    <div id="scheduleModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeScheduleModal()">&times;</span>
            <h2>Schedule Interview</h2>
            <form id="scheduleForm" method="POST" action="/{{ $routePrefix }}/interviews/schedule">
                @csrf
                <input type="hidden" id="applicationId" name="application_id">
                <input type="hidden" id="interview_date" name="interview_date">

                <div class="form-group">
                    <label for="interviewStartDate">Start Date & Time:</label>
                    <input type="datetime-local" id="interviewStartDate" name="interview_start_date" required>
                </div>
                <div class="form-group">
                    <label for="interviewEndDate">End Date & Time:</label>
                    <input type="datetime-local" id="interviewEndDate" name="interview_end_date" required>
                </div>

                <div class="form-group">
                    <label for="interviewLocation">Location (optional):</label>
                    <div style="display: flex; align-items: center; gap: 8px; position: relative;">
                        <input type="text" id="interviewLocation" name="interview_location" placeholder="e.g., Conference Room A or Video Call" style="flex:1;">
                        <button type="button" class="btn btn-outline-secondary" id="generateMeetBtn">Create Google Meet link</button>
                        <span id="meetLoadingIndicator" style="display:none; position:absolute; right:0; top:50%; transform:translateY(-50%); background:#fff; color:#6c63ff; font-weight:600; padding:2px 10px; border-radius:4px; font-size:13px; box-shadow:0 1px 4px rgba(0,0,0,0.07);">Generating Google Meet link...</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="interviewNotes">Additional Notes (optional):</label>
                    <textarea id="interviewNotes" name="additional_notes" rows="4" placeholder="Add any instructions or notes for the applicant..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Schedule</button>
                    <button type="button" class="btn btn-secondary" onclick="closeScheduleModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reschedule Interview Modal -->
    <div id="rescheduleModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeRescheduleModal()">&times;</span>
            <h2>Reschedule Interview</h2>
            <form id="rescheduleForm" method="POST" action="/{{ $routePrefix }}/interviews/reschedule">
                @csrf
                @method('PUT')
                <input type="hidden" id="rescheduleApplicationId" name="application_id">
                <input type="hidden" id="reschedule_interview_date" name="interview_date">

                <div class="form-group">
                    <label for="rescheduleInterviewStartDate">Start Date & Time:</label>
                    <input type="datetime-local" id="rescheduleInterviewStartDate" name="interview_start_date" required>
                </div>
                <div class="form-group">
                    <label for="rescheduleInterviewEndDate">End Date & Time:</label>
                    <input type="datetime-local" id="rescheduleInterviewEndDate" name="interview_end_date" required>
                </div>

                <div class="form-group">
                    <label for="rescheduleInterviewLocation">Location (optional):</label>
                    <div style="display: flex; align-items: center; gap: 8px; position: relative;">
                        <input type="text" id="rescheduleInterviewLocation" name="interview_location" placeholder="e.g., Conference Room A or Video Call" style="flex:1;">
                        <button type="button" class="btn btn-outline-secondary" id="rescheduleGenerateMeetBtn">Create Google Meet link</button>
                        <span id="rescheduleMeetLoadingIndicator" style="display:none; position:absolute; right:0; top:50%; transform:translateY(-50%); background:#fff; color:#6c63ff; font-weight:600; padding:2px 10px; border-radius:4px; font-size:13px; box-shadow:0 1px 4px rgba(0,0,0,0.07);">Generating Google Meet link...</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="rescheduleNotes">Reason for Rescheduling:</label>
                    <textarea id="rescheduleNotes" name="reschedule_reason" rows="4" placeholder="Brief explanation for rescheduling..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Reschedule</button>
                    <button type="button" class="btn btn-secondary" onclick="closeRescheduleModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Interview Notes Modal -->
    <div id="notesModal" class="notes-modal" aria-hidden="true">
        <div class="notes-modal-card" role="dialog" aria-modal="true" aria-labelledby="notesModalTitle">
            <div class="notes-modal-header">
                <div class="notes-chrome-dots" aria-hidden="true">
                    <span class="notes-chrome-dot red"></span>
                    <span class="notes-chrome-dot yellow"></span>
                    <span class="notes-chrome-dot green"></span>
                </div>
                <div class="notes-chrome-address">chrome-extension://interview-notes/popup.html</div>
                <button type="button" class="notes-modal-close" aria-label="Close" onclick="closeNotesModal()">&times;</button>
            </div>

            <div class="notes-modal-body">
                <h2 id="notesModalTitle" class="notes-modal-title">Interview Notes</h2>

                <form id="notesForm" method="POST" action="/{{ $routePrefix }}/interviews/addNotes" style="display: flex; flex-direction: column; flex: 1;">
                    @csrf
                    <input type="hidden" id="notesApplicationId" name="application_id">

                    <label for="interviewNotes" class="notes-form-label">Write notes for this interview</label>
                    <textarea id="interviewNotes" name="interview_notes" class="notes-form-textarea" placeholder="Add feedback, observations, or next steps from the interview..." required></textarea>

                    <div class="notes-modal-actions">
                        <button type="button" class="notes-modal-btn notes-modal-btn-secondary" onclick="closeNotesModal()">Cancel</button>
                        <button type="submit" class="notes-modal-btn notes-modal-btn-primary">Save Notes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.interviews-container {
    max-width: 1500px;
    padding-left: 0;
    padding-right: 0;
}

/* Summary Cards */
.summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.summary-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid #f0f0f0;
    transition: all 0.3s ease;
}

.summary-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.summary-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}

.summary-icon.pending {
    background: #fff3cd;
    color: #856404;
}

.summary-icon.scheduled {
    background: #d1ecf1;
    color: #0c5460;
}

.summary-icon.completed {
    background: #d4edda;
    color: #155724;
}

.summary-icon.rejected {
    background: #f8d7da;
    color: #721c24;
}

.summary-content {
    flex: 1;
}

.summary-label {
    font-size: 12px;
    font-weight: 600;
    color: #999;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.summary-value {
    font-size: 28px;
    font-weight: bold;
    color: var(--text-primary);
    margin-top: 4px;
}

.filter-section {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
    padding: 20px;
    background: var(--card-bg);
    border-radius: 8px;
    flex-wrap: wrap;
}

.filter-group {
    flex: 1;
    min-width: 200px;
}

.filter-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-primary);
    font-size: 14px;
}

.interviews-container .btn.btn-primary,
.interviews-container a.btn.btn-primary {
    color: #fff !important;
}

.filter-select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    background-color: #fff;
    cursor: pointer;
    transition: border-color 0.3s;
}

.filter-select:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(107, 70, 193, 0.1);
}

.interviews-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.interview-card {
    background: var(--card-bg);
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: box-shadow 0.3s;
}

.interview-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.interview-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 20px;
    border-bottom: 1px solid #f0f0f0;
    background: linear-gradient(135deg, #f8f9fa, #ffffff);
}

.interview-title h3 {
    color: var(--text-primary);
    font-size: 18px;
    margin-bottom: 5px;
}

.job-location {
    color: var(--text-secondary);
    font-size: 14px;
    margin: 0;
}

.interview-status {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
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

.interview-body {
    padding: 20px;
    display: grid;
    gap: 20px;
}

.applicant-info,
.application-details,
.salary-info,
.interview-schedule,
.interview-notes {
    background: #fafbfc;
    padding: 15px;
    border-radius: 6px;
    border-left: 4px solid var(--accent);
}

.applicant-info h4,
.application-details h4,
.salary-info h4,
.interview-schedule h4,
.interview-notes h4 {
    color: var(--text-primary);
    font-size: 16px;
    margin-bottom: 12px;
    margin-top: 0;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.info-item {
    font-size: 14px;
}

.info-item label {
    font-weight: 600;
    color: var(--text-secondary);
    display: block;
    margin-bottom: 4px;
}

.info-item p {
    color: var(--text-primary);
    margin: 0;
}

.info-item a {
    color: var(--accent);
    text-decoration: none;
}

.info-item a:hover {
    text-decoration: underline;
}

.salary-amount {
    font-size: 24px;
    font-weight: bold;
    color: var(--accent);
    margin: 0;
}

.schedule-info p {
    margin: 8px 0;
    color: var(--text-primary);
}

.schedule-info strong {
    color: var(--text-primary);
}

.application-details p,
.interview-schedule p,
.interview-notes p {
    color: var(--text-primary);
    line-height: 1.6;
    margin: 0;
}

.notes-content {
    background: white;
    padding: 12px;
    border-radius: 4px;
    border: 1px solid #e0e0e0;
    color: var(--text-primary);
    line-height: 1.6;
}

.btn-info {
    background: #17a2b8;
    color: white;
}

.btn-info:hover {
    background: #138496;
}

.interview-actions {
    padding: 15px 20px;
    background: #f8f9fa;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    border-top: 1px solid #f0f0f0;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
}

.btn-primary {
    background: var(--accent);
    color: white;
}

.btn-primary:hover {
    background: #5a3fa0;
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(107, 70, 193, 0.3);
}

.btn-secondary {
    background: var(--text-secondary);
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
}

.btn-warning {
    background: var(--warning);
    color: #000;
}

.btn-warning:hover {
    background: #e0a800;
}

.btn-danger {
    background: var(--danger);
    color: white;
}

.btn-danger:hover {
    background: #c82333;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: var(--card-bg);
    border-radius: 8px;
}

.empty-icon {
    font-size: 64px;
    color: #ddd;
    margin-bottom: 20px;
}

.empty-state h3 {
    color: var(--text-primary);
    margin-bottom: 10px;
}

.empty-state p {
    color: var(--text-secondary);
    margin-bottom: 20px;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    animation: fadeIn 0.3s;
}

@keyframes fadeIn {
    from {
        background: rgba(0, 0, 0, 0);
    }
    to {
        background: rgba(0, 0, 0, 0.5);
    }
}

.modal-content {
    background: var(--card-bg);
    margin: 5% auto;
    padding: 30px;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    animation: slideIn 0.3s;
}

@keyframes slideIn {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.close {
    color: var(--text-secondary);
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s;
}

.close:hover {
    color: var(--text-primary);
}

.modal h2 {
    color: var(--text-primary);
    margin-bottom: 20px;
    margin-top: 0;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-primary);
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-family: inherit;
    font-size: 14px;
    transition: border-color 0.3s;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(107, 70, 193, 0.1);
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 25px;
}

.form-actions .btn {
    flex: 1;
    justify-content: center;
}

.alert {
    padding: 15px 20px;
    margin-bottom: 20px;
    border-radius: 6px;
    font-size: 14px;
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

.notes-modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 2100;
    background: rgba(17, 24, 39, 0.38);
    align-items: center;
    justify-content: center;
    padding: 24px;
}

.notes-modal.is-open {
    display: flex;
}

.notes-modal-card {
    width: min(100%, 320px);
    min-height: 430px;
    background: #ffffff;
    border: 1px solid #a8b0bb;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 24px 50px rgba(15, 23, 42, 0.35);
    animation: notesModalPop 0.22s ease-out;
}

@keyframes notesModalPop {
    from {
        opacity: 0;
        transform: translateY(18px) scale(0.98);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.notes-modal-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    background: linear-gradient(180deg, #4b4f56 0%, #30343a 100%);
    border-bottom: 1px solid #22272d;
}

.notes-chrome-dots {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}

.notes-chrome-dot {
    width: 10px;
    height: 10px;
    border-radius: 999px;
    display: inline-block;
}

.notes-chrome-dot.red {
    background: #ff5f57;
}

.notes-chrome-dot.yellow {
    background: #febc2e;
}

.notes-chrome-dot.green {
    background: #28c840;
}

.notes-chrome-address {
    flex: 1;
    min-width: 0;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 999px;
    color: #e5e7eb;
    font-size: 11px;
    line-height: 1;
    padding: 7px 12px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.notes-modal-close {
    border: none;
    background: transparent;
    color: #d1d5db;
    font-size: 18px;
    line-height: 1;
    cursor: pointer;
    padding: 0 2px;
    flex-shrink: 0;
}

.notes-modal-close:hover {
    color: #ffffff;
}

.notes-modal-body {
    padding: 18px 16px 16px;
    display: flex;
    flex-direction: column;
    min-height: 382px;
    background:
        linear-gradient(180deg, rgba(241, 245, 249, 0.9) 0%, rgba(255, 255, 255, 1) 14%),
        #ffffff;
}

.notes-modal-title {
    color: #1f2937;
    margin: 0 0 14px;
    font-size: 18px;
    font-weight: 700;
}

.notes-form-label {
    display: block;
    margin-bottom: 8px;
    color: #374151;
    font-size: 13px;
    font-weight: 600;
}

.notes-form-textarea {
    width: 100%;
    min-height: 190px;
    resize: vertical;
    border: 1px solid #cfd6df;
    border-radius: 6px;
    padding: 12px;
    font-size: 14px;
    color: #334155;
    box-sizing: border-box;
    background: #ffffff;
}

.notes-form-textarea:focus {
    outline: none;
    border-color: #4f8df7;
    box-shadow: 0 0 0 3px rgba(79, 141, 247, 0.16);
}

.notes-modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: auto;
    padding-top: 16px;
}

.notes-modal-btn {
    border-radius: 7px;
    padding: 10px 16px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.notes-modal-btn:hover {
    opacity: 0.92;
    transform: translateY(-1px);
}

.notes-modal-btn-primary {
    background: #4f8df7;
    color: #fff;
    border: 1px solid #3a78df;
}

.notes-modal-btn-secondary {
    background: #f8fafc;
    color: #475569;
    border: 1px solid #cbd5e1;
}

@media (max-width: 768px) {
    .filter-section {
        flex-direction: column;
    }

    .interview-header {
        flex-direction: column;
        gap: 10px;
    }

    .interview-status {
        justify-content: flex-start;
    }

    .info-grid {
        grid-template-columns: 1fr;
    }

    .interview-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }

    .modal-content {
        width: 95%;
        margin: 50% auto;
    }
}
</style>

<script>
const routePrefix = @json($routePrefix);
const csrfToken = @json(csrf_token());
function openCreateModal() {
    document.getElementById('createInterviewModal').style.display = 'block';
}

function closeCreateModal() {
    document.getElementById('createInterviewModal').style.display = 'none';
}

function openScheduleModal(applicationId) {
    document.getElementById('applicationId').value = applicationId;
    document.getElementById('scheduleModal').style.display = 'block';
    // Clear previous location value
    var locationInput = document.getElementById('interviewLocation');
    locationInput.value = '';
    locationInput.placeholder = 'Enter interview location or link';
}

function closeScheduleModal() {
    document.getElementById('scheduleModal').style.display = 'none';
}

function openRescheduleModal(applicationId) {
    document.getElementById('rescheduleApplicationId').value = applicationId;
    document.getElementById('rescheduleModal').style.display = 'block';
}

function closeRescheduleModal() {
    document.getElementById('rescheduleModal').style.display = 'none';
}

function openNotesModal(applicationId) {
    const popup = window.open(
        '',
        'interviewNotesPopup',
        'toolbar=no,scrollbars=yes,resizable=yes,top=120,left=420,width=440,height=520'
    );

    if (!popup) {
        alert('Please allow popups for this page to add interview notes.');
        return;
    }

    const doc = popup.document;
    doc.open();
    doc.close();
    doc.title = 'Interview Notes';
    doc.documentElement.lang = 'en';

    const metaCharset = doc.createElement('meta');
    metaCharset.setAttribute('charset', 'UTF-8');

    const metaViewport = doc.createElement('meta');
    metaViewport.name = 'viewport';
    metaViewport.content = 'width=device-width, initial-scale=1.0';

    const style = doc.createElement('style');
    style.textContent = `
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background: #f8fafc;
            color: #1f2937;
        }
        h1 {
            margin: 0 0 16px;
            font-size: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
        }
        textarea {
            width: 100%;
            min-height: 260px;
            box-sizing: border-box;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font: inherit;
            resize: vertical;
        }
        textarea:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }
        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 16px;
        }
        button {
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            font: inherit;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-cancel {
            background: #e2e8f0;
            color: #334155;
        }
        .btn-save {
            background: #4f46e5;
            color: #fff;
        }
    `;

    doc.head.replaceChildren(metaCharset, metaViewport, style);

    const heading = doc.createElement('h1');
    heading.textContent = 'Add Interview Notes';

    const form = doc.createElement('form');
    form.method = 'POST';
    form.action = '/' + routePrefix + '/interviews/addNotes';

    const tokenInput = doc.createElement('input');
    tokenInput.type = 'hidden';
    tokenInput.name = '_token';
    tokenInput.value = csrfToken;

    const applicationInput = doc.createElement('input');
    applicationInput.type = 'hidden';
    applicationInput.name = 'application_id';
    applicationInput.value = applicationId;

    const label = doc.createElement('label');
    label.htmlFor = 'popupInterviewNotes';
    label.textContent = 'Interview Notes';

    const textarea = doc.createElement('textarea');
    textarea.id = 'popupInterviewNotes';
    textarea.name = 'interview_notes';
    textarea.placeholder = 'Add feedback, observations, or next steps from the interview...';
    textarea.required = true;

    const actions = doc.createElement('div');
    actions.className = 'actions';

    const cancelButton = doc.createElement('button');
    cancelButton.type = 'button';
    cancelButton.className = 'btn-cancel';
    cancelButton.textContent = 'Cancel';
    cancelButton.addEventListener('click', function() {
        popup.close();
    });

    const saveButton = doc.createElement('button');
    saveButton.type = 'submit';
    saveButton.className = 'btn-save';
    saveButton.textContent = 'Save Notes';

    actions.append(cancelButton, saveButton);
    form.append(tokenInput, applicationInput, label, textarea, actions);
    doc.body.replaceChildren(heading, form);

    popup.focus();
    textarea.focus();
}

function closeNotesModal() {
    document.getElementById('notesModal').classList.remove('is-open');
    document.getElementById('notesModal').setAttribute('aria-hidden', 'true');
}

function rejectApplication(applicationId) {
    if (confirm('Are you sure you want to reject this application? A rejection email will be sent to the applicant once you confirm.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/' + routePrefix + '/interviews/reject/' + applicationId;
        form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                         '<input type="hidden" name="_method" value="DELETE">';
        document.body.appendChild(form);
        form.submit();
    }
}

function filterInterviews() {
    const jobFilter = document.getElementById('job-filter').value;
    const statusFilter = document.getElementById('status-filter').value;
    const rows = document.querySelectorAll('.table-row');

    console.log('Filtering - Job:', jobFilter, 'Status:', statusFilter, 'Rows:', rows.length);

    rows.forEach(row => {
        let show = true;
        const rowJobId = row.getAttribute('data-job-id');
        const rowStatus = row.getAttribute('data-status');
        
        console.log('Row - JobID:', rowJobId, 'Status:', rowStatus);

        if (jobFilter && rowJobId !== jobFilter) {
            show = false;
        }

        if (statusFilter && rowStatus !== statusFilter) {
            show = false;
        }

        row.style.display = show ? 'table-row' : 'none';
        console.log('Show row:', show);
    });
}

// Apply filters on page load
document.addEventListener('DOMContentLoaded', function() {
    // Reset filter dropdowns to "All" on page load
    document.getElementById('job-filter').value = '';
    document.getElementById('status-filter').value = '';
    
    setTimeout(function() {
        filterInterviews();
    }, 100);
});

function completeInterview(applicationId) {
    if (confirm('Mark this interview as completed?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/' + routePrefix + '/interviews/complete/' + applicationId;
        form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
        document.body.appendChild(form);
        form.submit();
    }
}

function viewDetails(applicationId) {
    // This could open a detailed view or redirect to an application details page
    window.location.href = '/' + routePrefix + '/interviews/' + applicationId;
}

// Close modal when clicking outside
window.onclick = function(event) {
    const scheduleModal = document.getElementById('scheduleModal');
    const rescheduleModal = document.getElementById('rescheduleModal');
    const notesModal = document.getElementById('notesModal');
    if (event.target === scheduleModal) {
        scheduleModal.style.display = 'none';
    }
    if (event.target === rescheduleModal) {
        rescheduleModal.style.display = 'none';
    }
    if (event.target === notesModal) {
        closeNotesModal();
    }
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeNotesModal();
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {

    // Only save to database on submit (default form action)
    // Add Make.com webhook integration on Google Meet button click
    var meetBtn = document.getElementById('generateMeetBtn');
    if (meetBtn) {
        meetBtn.addEventListener('click', function() {
                        var loadingIndicator = document.getElementById('meetLoadingIndicator');
                        if (loadingIndicator) loadingIndicator.style.display = 'inline';
            // ...existing code for webhook...
            var applicantName = '';
            var applicantEmail = '';
            var applicationId = document.getElementById('applicationId').value;
            var interviewStartDate = document.getElementById('interviewStartDate').value;
            var interviewEndDate = document.getElementById('interviewEndDate').value;

            var row = document.querySelector('button[onclick="openScheduleModal(' + applicationId + ')"]').closest('tr');
            if (row) {
                var nameCell = row.querySelector('td:nth-child(3)');
                if (nameCell) {
                    applicantName = nameCell.textContent.trim();
                }
                var emailCell = row.querySelector('td:nth-child(4) a');
                if (emailCell) {
                    applicantEmail = emailCell.textContent.trim();
                }
            }

            fetch('https://hook.us2.make.com/ey8o1k4wfsk04e8own7uy396am2x5fp9', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    interview_type: 'Interview',
                    applicant_name: applicantName,
                    applicant_email: applicantEmail,
                    start_datetime: interviewStartDate,
                    end_datetime: interviewEndDate,
                })
            })
            .then(response => {
                if (!response.ok) throw new Error('Webhook request failed');
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        return text;
                    }
                });
            })
            .then(data => {
                // Set the returned link to the location field if it's a valid URL
                var locationInput = document.getElementById('interviewLocation');
                if (typeof data === 'string' && data.startsWith('http')) {
                    locationInput.value = data;
                    locationInput.placeholder = 'Google Meet link generated';
                }
                if (loadingIndicator) loadingIndicator.style.display = 'none';
            })
            .catch(error => {
                console.error('Webhook error:', error);
                if (loadingIndicator) loadingIndicator.style.display = 'none';
            });
        });
    }

    // On form submit, set interview_date to interviewStartDate
    var scheduleForm = document.getElementById('scheduleForm');
    if (scheduleForm) {
        scheduleForm.addEventListener('submit', function(e) {
            var interviewStartDate = document.getElementById('interviewStartDate').value;
            document.getElementById('interview_date').value = interviewStartDate;
        });
    }

    var rescheduleMeetBtn = document.getElementById('rescheduleGenerateMeetBtn');
        if (rescheduleMeetBtn) {
            rescheduleMeetBtn.addEventListener('click', function() {
                alert('Generating Google Meet link for rescheduled interview. Please wait...');
                var loadingIndicator = document.getElementById('rescheduleMeetLoadingIndicator');
                if (loadingIndicator) loadingIndicator.style.display = 'inline';
                // Get values for webhook (reuse logic as needed)
                var applicantName = '';
                var applicantEmail = '';
                var applicationId = document.getElementById('rescheduleApplicationId').value;
                var interviewStartDate = document.getElementById('rescheduleInterviewStartDate').value;
                var interviewEndDate = document.getElementById('rescheduleInterviewEndDate').value;

                // Try to get applicant name and email from the table row (if available)
                var row = document.querySelector('button[onclick="openRescheduleModal(' + applicationId + ')"]').closest('tr');
                if (row) {
                    var nameCell = row.querySelector('td:nth-child(3)');
                    if (nameCell) {
                        applicantName = nameCell.textContent.trim();
                    }
                    var emailCell = row.querySelector('td:nth-child(4) a');
                    if (emailCell) {
                        applicantEmail = emailCell.textContent.trim();
                    }
                }

                fetch('https://hook.us2.make.com/ey8o1k4wfsk04e8own7uy396am2x5fp9', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        interview_type: 'Interview',
                        applicant_name: applicantName,
                        applicant_email: applicantEmail,
                        start_datetime: interviewStartDate,
                        end_datetime: interviewEndDate,
                    })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Webhook request failed');
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            return text;
                        }
                    });
                })
                .then(data => {
                    var locationInput = document.getElementById('rescheduleInterviewLocation');
                    if (typeof data === 'string' && data.startsWith('http')) {
                        locationInput.value = data;
                        locationInput.placeholder = 'Google Meet link generated';
                    }
                    if (loadingIndicator) loadingIndicator.style.display = 'none';
                })
                .catch(error => {
                    console.error('Webhook error:', error);
                    if (loadingIndicator) loadingIndicator.style.display = 'none';
                });
            });
        // }
        }
        // On reschedule form submit, set interview_date to Start date
        var rescheduleForm = document.getElementById('rescheduleForm');
        if (rescheduleForm) {
            rescheduleForm.addEventListener('submit', function(e) {
                var interviewStartDate = document.getElementById('rescheduleInterviewStartDate').value;
                document.getElementById('reschedule_interview_date').value = interviewStartDate;
            });
        }

});
</script>
@endsection
