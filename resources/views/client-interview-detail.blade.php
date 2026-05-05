@extends('layouts.dashboard')

@section('page-title', 'Interview Details')

@php $routePrefix = session('user')['accounttype'] ?? 'client'; @endphp

@section('content')
<div class="dashboard-content">
    <div class="dashboard-header">
        <div style="display: flex; align-items: center; gap: 15px; width: 100%;">
            <a href="/{{ $routePrefix }}/interviews" class="back-link">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1>Interview Details</h1>
            </div>
        </div>
    </div>

    <div class="detail-container">
        <!-- Status Section -->
        <div class="detail-card">
            <div class="card-header">
                <h2>Application Status</h2>
                <div class="status-display">
                    @if($application->interview_status == 'pending' || !$application->interview_status)
                        <span class="status-badge status-pending">Pending Interview</span>
                    @elseif($application->interview_status == 'scheduled')
                        <span class="status-badge status-scheduled">Scheduled</span>
                    @elseif($application->interview_status == 'completed')
                        <span class="status-badge status-completed">Completed</span>
                    @elseif($application->interview_status == 'rejected')
                        <span class="status-badge status-rejected">Rejected</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Job Posting Information -->
        <div class="detail-card">
            <div class="card-header">
                <h2>Job Position Details</h2>
            </div>
            <div class="card-body">
                <div class="info-group">
                    <h3>{{ $application->jobPosting->title ?? 'N/A' }}</h3>
                    <div class="info-row">
                        <div class="info-col">
                            <label>Location:</label>
                            <p>{{ $application->jobPosting->location ?? 'Not specified' }}</p>
                        </div>
                        <div class="info-col">
                            <label>Job Type:</label>
                            <p>{{ $application->jobPosting->job_type ?? 'Not specified' }}</p>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-col">
                            <label>Description:</label>
                            <p>{{ $application->jobPosting->description ?? 'No description provided' }}</p>
                        </div>
                    </div>
                    @if($application->jobPosting->salary_range)
                        <div class="info-row">
                            <div class="info-col">
                                <label>Salary Range:</label>
                                <p>{{ $application->jobPosting->salary_range }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Applicant Information -->
        <div class="detail-card">
            <div class="card-header">
                <h2>Applicant Information</h2>
            </div>
            <div class="card-body">
                <div class="applicant-card">
                    <div class="applicant-header">
                        <div>
                            <h3>{{ $application->applicant->first_name ?? 'N/A' }} {{ $application->applicant->last_name ?? '' }}</h3>
                            <p class="applicant-type">Healthcare Professional</p>
                        </div>
                    </div>
                    <div class="applicant-details">
                        <div class="detail-row">
                            <label>Email:</label>
                            <a href="mailto:{{ $application->applicant->email }}">{{ $application->applicant->email }}</a>
                        </div>
                        @if($application->applicant->phone)
                            <div class="detail-row">
                                <label>Phone:</label>
                                <a href="tel:{{ $application->applicant->phone }}">{{ $application->applicant->phone }}</a>
                            </div>
                        @endif
                        <div class="detail-row">
                            <label>Applied Date:</label>
                            <span>{{ $application->created_at->format('F d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Application Details -->
        <div class="detail-card">
            <div class="card-header">
                <h2>Application Submission</h2>
            </div>
            <div class="card-body">
                <div class="application-content">
                    <div class="detail-section">
                        <h4>Applicant Statement</h4>
                        <p>{{ $application->application_details ?? 'No details provided' }}</p>
                    </div>
                    @if($application->expected_salary)
                        <div class="detail-section">
                            <h4>Expected Salary</h4>
                            <p class="salary-display">${{ number_format($application->expected_salary, 2) }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Interview Schedule (if scheduled) -->
        @if($application->interview_status == 'scheduled' && $application->interview_date)
            <div class="detail-card interview-scheduled">
                <div class="card-header">
                    <h2>Scheduled Interview</h2>
                </div>
                <div class="card-body">
                    <div class="interview-details">
                        <div class="interview-item">
                            <div class="interview-icon">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="interview-info">
                                <label>Date & Time</label>
                                <p>{{ \Carbon\Carbon::parse($application->interview_date)->format('l, F j, Y') }}</p>
                                <p class="time">{{ \Carbon\Carbon::parse($application->interview_date)->format('g:i A') }}</p>
                            </div>
                        </div>

                        @if($application->interview_location)
                            <div class="interview-item">
                                <div class="interview-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="interview-info">
                                    <label>Location</label>
                                    <p>{{ $application->interview_location }}</p>
                                </div>
                            </div>
                        @endif

                        @if($application->interview_notes)
                            <div class="interview-item full-width">
                                <div class="interview-icon">
                                    <i class="fas fa-sticky-note"></i>
                                </div>
                                <div class="interview-info">
                                    <label>Notes</label>
                                    <p>{{ $application->interview_notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="action-buttons">
            @if(!$application->interview_status || $application->interview_status == 'pending')
                <button class="btn btn-primary" onclick="openScheduleModal()">
                    <i class="fas fa-calendar-plus"></i> Schedule Interview
                </button>
            @endif

            @if($application->interview_status == 'scheduled')
                <button class="btn btn-warning" onclick="openRescheduleModal()">
                    <i class="fas fa-edit"></i> Reschedule Interview
                </button>
            @endif

            @if($application->interview_status !== 'rejected')
                <button class="btn btn-danger" onclick="rejectApplication()">
                    <i class="fas fa-times-circle"></i> Reject Application
                </button>
            @endif

            <a href="/{{ $routePrefix }}/interviews" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Interviews
            </a>
        </div>
    </div>

    <!-- Schedule Modal -->
    <div id="scheduleModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeScheduleModal()">&times;</span>
            <h2>Schedule Interview</h2>
            <form method="POST" action="/{{ $routePrefix }}/interviews/schedule">
                @csrf
                <input type="hidden" name="application_id" value="{{ $application->id }}">

                <div class="form-group">
                    <label for="interviewDate">Interview Date & Time:</label>
                    <input type="datetime-local" id="interviewDate" name="interview_date" required>
                </div>

                <div class="form-group">
                    <label for="interviewLocation">Location (optional):</label>
                    <input type="text" id="interviewLocation" name="interview_location" placeholder="e.g., Conference Room A or Video Call">
                </div>

                <div class="form-group">
                    <label for="interviewNotes">Additional Notes (optional):</label>
                    <textarea id="interviewNotes" name="interview_notes" rows="4" placeholder="Add any instructions or notes for the applicant..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Schedule</button>
                    <button type="button" class="btn btn-secondary" onclick="closeScheduleModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reschedule Modal -->
    <div id="rescheduleModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeRescheduleModal()">&times;</span>
            <h2>Reschedule Interview</h2>
            <form method="POST" action="/{{ $routePrefix }}/interviews/reschedule">
                @csrf
                @method('PUT')
                <input type="hidden" name="application_id" value="{{ $application->id }}">

                <div class="form-group">
                    <label for="newInterviewDate">New Interview Date & Time:</label>
                    <input type="datetime-local" id="newInterviewDate" name="interview_date" required 
                           @if($application->interview_date) value="{{ \Carbon\Carbon::parse($application->interview_date)->format('Y-m-d\TH:i') }}" @endif>
                </div>

                <div class="form-group">
                    <label for="newInterviewLocation">Location (optional):</label>
                    <input type="text" id="newInterviewLocation" name="interview_location" 
                           value="{{ $application->interview_location ?? '' }}" placeholder="e.g., Conference Room A or Video Call">
                </div>

                <div class="form-group">
                    <label for="rescheduleNotes">Reason for Rescheduling:</label>
                    <textarea id="rescheduleNotes" name="interview_notes" rows="4" placeholder="Brief explanation for rescheduling...">{{ $application->interview_notes ?? '' }}</textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Reschedule</button>
                    <button type="button" class="btn btn-secondary" onclick="closeRescheduleModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.detail-container {
    max-width: 900px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.back-link {
    display: inline-flex;
    align-items: center;
    color: var(--accent);
    text-decoration: none;
    font-size: 18px;
    cursor: pointer;
    transition: color 0.3s;
}

.back-link:hover {
    color: #5a3fa0;
}

.detail-card {
    background: var(--card-bg);
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: box-shadow 0.3s;
}

.detail-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.card-header {
    padding: 20px;
    border-bottom: 1px solid #f0f0f0;
    background: linear-gradient(135deg, #f8f9fa, #ffffff);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h2 {
    color: var(--text-primary);
    font-size: 18px;
    margin: 0;
}

.status-display {
    display: flex;
    gap: 10px;
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

.card-body {
    padding: 20px;
}

.info-group h3 {
    color: var(--text-primary);
    font-size: 16px;
    margin-bottom: 15px;
}

.info-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 15px;
}

.info-col {
    background: #fafbfc;
    padding: 12px;
    border-radius: 6px;
}

.info-col label {
    font-weight: 600;
    color: var(--text-secondary);
    font-size: 13px;
    display: block;
    margin-bottom: 5px;
}

.info-col p {
    color: var(--text-primary);
    margin: 0;
    line-height: 1.5;
}

.applicant-card {
    background: linear-gradient(135deg, #f8f9fa, #ffffff);
    border-radius: 6px;
    overflow: hidden;
}

.applicant-header {
    padding: 15px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    gap: 15px;
}

.applicant-header h3 {
    color: var(--text-primary);
    font-size: 16px;
    margin: 0 0 5px 0;
}

.applicant-type {
    color: var(--text-secondary);
    font-size: 13px;
    margin: 0;
}

.applicant-details {
    padding: 15px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #e9ecef;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-row label {
    font-weight: 600;
    color: var(--text-secondary);
    font-size: 13px;
}

.detail-row a,
.detail-row span {
    color: var(--text-primary);
    text-decoration: none;
}

.detail-row a:hover {
    color: var(--accent);
    text-decoration: underline;
}

.application-content {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.detail-section {
    background: #fafbfc;
    padding: 15px;
    border-radius: 6px;
    border-left: 4px solid var(--accent);
}

.detail-section h4 {
    color: var(--text-primary);
    font-size: 14px;
    margin: 0 0 10px 0;
}

.detail-section p {
    color: var(--text-primary);
    margin: 0;
    line-height: 1.6;
}

.salary-display {
    font-size: 24px;
    font-weight: bold;
    color: var(--accent);
    margin: 0;
}

.interview-scheduled {
    background: linear-gradient(135deg, #d1ecf1, #ffffff);
    border-left: 4px solid #0c5460;
}

.interview-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.interview-item {
    display: flex;
    gap: 15px;
}

.interview-item.full-width {
    grid-column: 1 / -1;
}

.interview-icon {
    font-size: 24px;
    color: var(--accent);
    min-width: 30px;
    text-align: center;
    margin-top: 3px;
}

.interview-info label {
    font-weight: 600;
    color: var(--text-secondary);
    font-size: 12px;
    display: block;
    margin-bottom: 4px;
}

.interview-info p {
    color: var(--text-primary);
    margin: 0;
    line-height: 1.5;
}

.interview-info .time {
    font-weight: 600;
    font-size: 16px;
}

.action-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 20px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-primary {
    background: var(--accent);
    color: white;
}

.btn-primary:hover {
    background: #5a3fa0;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(107, 70, 193, 0.3);
}

.btn-secondary {
    background: var(--text-secondary);
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

.btn-warning {
    background: var(--warning);
    color: #000;
}

.btn-warning:hover {
    background: #e0a800;
    transform: translateY(-2px);
}

.btn-danger {
    background: var(--danger);
    color: white;
}

.btn-danger:hover {
    background: #c82333;
    transform: translateY(-2px);
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
    font-size: 14px;
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

@media (max-width: 768px) {
    .card-header {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }

    .status-display {
        width: 100%;
    }

    .interview-details {
        grid-template-columns: 1fr;
    }

    .action-buttons {
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
function openScheduleModal() {
    document.getElementById('scheduleModal').style.display = 'block';
}

function closeScheduleModal() {
    document.getElementById('scheduleModal').style.display = 'none';
}

function openRescheduleModal() {
    document.getElementById('rescheduleModal').style.display = 'block';
}

function closeRescheduleModal() {
    document.getElementById('rescheduleModal').style.display = 'none';
}

function rejectApplication() {
    if (confirm('Are you sure you want to reject this application? A rejection email will be sent to the applicant once you confirm.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/{{ $routePrefix }}/interviews/reject/{{ $application->id }}';
        form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                         '<input type="hidden" name="_method" value="DELETE">';
        document.body.appendChild(form);
        form.submit();
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const scheduleModal = document.getElementById('scheduleModal');
    const rescheduleModal = document.getElementById('rescheduleModal');
    if (event.target === scheduleModal) {
        scheduleModal.style.display = 'none';
    }
    if (event.target === rescheduleModal) {
        rescheduleModal.style.display = 'none';
    }
}
</script>
@endsection
