@extends('layouts.dashboard')

@section('page-title', 'Applications')

@php
    $statusLabels = [
        'pending' => 'Pending Review',
        'scheduled' => 'Interview Scheduled',
        'rescheduled' => 'Interview Rescheduled',
        'completed' => 'Completed',
        'hired' => 'Hired',
        'rejected' => 'Rejected',
    ];
@endphp

@section('content')
<div class="dashboard-content">
    <div class="dashboard-header">
        <h1>Your Applications</h1>
    </div>

    @if(session('success'))
        <div class="page-alert page-alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="page-alert page-alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="application-stats">
        <div class="stat-tile">
            <span class="stat-label">Total Submitted</span>
            <strong>{{ $applications->count() }}</strong>
        </div>
        <div class="stat-tile">
            <span class="stat-label">Pending</span>
            <strong>{{ $applications->where('interview_status', 'pending')->count() + $applications->whereNull('interview_status')->count() }}</strong>
        </div>
        <div class="stat-tile">
            <span class="stat-label">Scheduled</span>
            <strong>{{ $applications->whereIn('interview_status', ['scheduled', 'rescheduled'])->count() }}</strong>
        </div>
        <div class="stat-tile">
            <span class="stat-label">Completed</span>
            <strong>{{ $applications->whereIn('interview_status', ['completed', 'hired'])->count() }}</strong>
        </div>
    </div>

    @if($applications->isEmpty())
        <div class="empty-state-card">
            <div class="empty-icon"><i class="fas fa-file-medical"></i></div>
            <h3>No applications submitted yet</h3>
            <p>Once you apply to a job, it will appear here so you can track, edit, or delete it.</p>
            <a href="/healthcare-jobs" class="browse-jobs-btn">Browse Jobs</a>
        </div>
    @else
        <div class="table-card">
            <div class="table-wrapper">
                <table class="applications-table">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Client</th>
                            <th>Location</th>
                            <th>Submitted</th>
                            <th>Expected Salary</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $application)
                            @php
                                $statusKey = $application->interview_status ?: 'pending';
                                $statusLabel = $statusLabels[$statusKey] ?? ucfirst($statusKey);
                                $clientName = $application->jobPosting->client->alias ?: trim(($application->jobPosting->client->first_name ?? '') . ' ' . ($application->jobPosting->client->last_name ?? ''));
                                $isHired = $statusKey === 'hired';
                            @endphp
                            <tr>
                                <td>
                                    <div class="job-title-cell">
                                        <strong>{{ $application->jobPosting->title ?? 'Job posting unavailable' }}</strong>
                                        <small>{{ \Illuminate\Support\Str::limit($application->application_details ?: 'No message provided.', 70) }}</small>
                                    </div>
                                </td>
                                <td>{{ $clientName !== '' ? $clientName : 'Client not available' }}</td>
                                <td>{{ $application->jobPosting->location ?? 'Location not specified' }}</td>
                                <td>
                                    <div class="date-cell">
                                        <strong>{{ optional($application->created_at)->format('M d, Y') }}</strong>
                                        <small>{{ optional($application->created_at)->diffForHumans() }}</small>
                                    </div>
                                </td>
                                <td>{{ $application->expected_salary ?: 'Not provided' }}</td>
                                <td><span class="status-badge status-{{ $statusKey }}">{{ $statusLabel }}</span></td>
                                <td>
                                    @if(!$isHired)
                                        <div class="table-actions">
                                            <button type="button" class="primary-btn" onclick="openEditModal({{ $application->id }})">Edit</button>
                                            <form method="POST" action="/applications/{{ $application->id }}" onsubmit="return confirm('Delete this application? This cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="danger-btn">Delete</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="muted-text">No actions available</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @foreach($applications as $application)
            <div class="modal-backdrop" id="editModal{{ $application->id }}">
                <div class="edit-modal">
                    <div class="modal-top">
                        <div>
                            <h2>Edit Application</h2>
                            <p>{{ $application->jobPosting->title ?? 'Application' }}</p>
                        </div>
                        <button type="button" class="modal-close-btn" onclick="closeEditModal({{ $application->id }})">&times;</button>
                    </div>

                    <form method="POST" action="/applications/{{ $application->id }}" enctype="multipart/form-data" class="edit-form">
                        @csrf
                        @method('PUT')

                        <label for="expected_salary_{{ $application->id }}">Expected Salary</label>
                        <input
                            id="expected_salary_{{ $application->id }}"
                            name="expected_salary"
                            type="text"
                            value="{{ old('expected_salary', $application->expected_salary) }}"
                            placeholder="e.g. $45/hr or 85000"
                        >

                        <label for="application_details_{{ $application->id }}">Application Details</label>
                        <textarea
                            id="application_details_{{ $application->id }}"
                            name="application_details"
                            rows="6"
                            placeholder="Write a short message about your experience and fit for the role."
                        >{{ old('application_details', $application->application_details) }}</textarea>

                        <label for="attachment_{{ $application->id }}">Replace Attachment</label>
                        <input
                            id="attachment_{{ $application->id }}"
                            name="attachment"
                            type="file"
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt"
                        >

                        @if($application->attachments)
                            <label class="checkbox-row">
                                <input type="checkbox" name="remove_attachment" value="1">
                                Remove current attachment
                            </label>
                        @endif

                        <div class="modal-actions">
                            <button type="button" class="secondary-btn" onclick="closeEditModal({{ $application->id }})">Cancel</button>
                            <button type="submit" class="primary-btn">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endif
</div>

<style>
    .page-alert {
        padding: 14px 18px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .page-alert-success {
        background: #d4edda;
        color: #155724;
    }

    .page-alert-danger {
        background: #f8d7da;
        color: #842029;
    }

    .application-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-tile {
        background: white;
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid #ece8fb;
    }

    .stat-tile strong {
        display: block;
        font-size: 28px;
        color: var(--accent);
        margin-top: 8px;
    }

    .stat-label {
        color: #6b7280;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .table-card {
        background: white;
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 4px 16px rgba(33, 37, 41, 0.08);
        border: 1px solid #ede9fe;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .applications-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1100px;
    }

    .applications-table thead {
        background: #f8f7ff;
    }

    .applications-table th {
        text-align: left;
        padding: 14px 12px;
        font-size: 13px;
        color: #4b5563;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        border-bottom: 1px solid #e5e7eb;
    }

    .applications-table td {
        padding: 16px 12px;
        vertical-align: top;
        border-bottom: 1px solid #f1f5f9;
        color: #374151;
        font-size: 14px;
    }

    .applications-table tbody tr:hover {
        background: #fafaff;
    }

    .job-title-cell,
    .date-cell {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .job-title-cell strong,
    .date-cell strong {
        color: #1f2937;
    }

    .job-title-cell small,
    .date-cell small {
        color: #6b7280;
        line-height: 1.5;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        white-space: nowrap;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-scheduled,
    .status-rescheduled {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .status-completed,
    .status-hired {
        background: #d1fae5;
        color: #065f46;
    }

    .status-rejected {
        background: #fee2e2;
        color: #b91c1c;
    }

    .muted-text {
        color: #6b7280;
    }

    .table-actions {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    .table-actions form {
        margin: 0;
    }

    .primary-btn,
    .secondary-btn,
    .danger-btn,
    .browse-jobs-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 8px 14px;
        border-radius: 10px;
        border: none;
        text-decoration: none;
        cursor: pointer;
        font-weight: 600;
        transition: 0.2s ease;
    }

    .primary-btn {
        background: var(--accent);
        color: white;
    }

    .primary-btn:hover,
    .browse-jobs-btn:hover {
        background: #563aa9;
    }

    .secondary-btn {
        background: #ede9fe;
        color: var(--accent);
    }

    .secondary-btn:hover {
        background: #ddd6fe;
    }

    .danger-btn {
        background: #dc3545;
        color: white;
    }

    .danger-btn:hover {
        background: #bb2d3b;
    }

    .browse-jobs-btn {
        background: var(--accent);
        color: white;
        margin-top: 10px;
    }

    .empty-state-card {
        background: white;
        border-radius: 18px;
        padding: 48px 24px;
        text-align: center;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .empty-icon {
        width: 72px;
        height: 72px;
        margin: 0 auto 18px;
        border-radius: 50%;
        background: #efe9ff;
        color: var(--accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }

    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        z-index: 2000;
        padding: 20px;
        overflow-y: auto;
    }

    .modal-backdrop.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .edit-modal {
        width: 100%;
        max-width: 640px;
        background: white;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.24);
    }

    .modal-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 20px;
    }

    .modal-top h2 {
        margin: 0 0 4px;
    }

    .modal-top p {
        margin: 0;
        color: #6b7280;
    }

    .modal-close-btn {
        border: none;
        background: transparent;
        font-size: 30px;
        line-height: 1;
        cursor: pointer;
        color: #6b7280;
    }

    .edit-form {
        display: grid;
        gap: 12px;
    }

    .edit-form label {
        font-weight: 600;
        color: #374151;
    }

    .edit-form input[type="text"],
    .edit-form input[type="file"],
    .edit-form textarea {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 12px 14px;
        font: inherit;
    }

    .edit-form textarea {
        resize: vertical;
    }

    .checkbox-row {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #4b5563;
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 8px;
        flex-wrap: wrap;
    }
</style>

<script>
    function openEditModal(applicationId) {
        const modal = document.getElementById(`editModal${applicationId}`);
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeEditModal(applicationId) {
        const modal = document.getElementById(`editModal${applicationId}`);
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    document.querySelectorAll('.modal-backdrop').forEach((modal) => {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });
</script>
@endsection
