@extends('layouts.dashboard')

@section('page-title', 'Endorsed Workers')

@section('content')
<div class="dashboard-content">
    <style>
        .endorsed-workers-card {
            width: 100%;
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 18px;
            box-shadow: 0 14px 34px rgba(44, 62, 80, 0.08);
            overflow: hidden;
        }

        .endorsed-workers-card.empty {
            padding: 24px;
        }

        .endorsed-workers-table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .endorsed-workers-table {
            width: 100%;
            min-width: 1120px;
            border-collapse: collapse;
        }

        .endorsed-workers-table thead tr {
            background: #f8fafc;
        }

        .endorsed-workers-table th {
            padding: 18px 20px;
            text-align: left;
            font-size: 14px;
            font-weight: 700;
            color: #1f3b5b;
            border-bottom: 1px solid #dfe7ef;
            white-space: nowrap;
        }

        .endorsed-workers-table td {
            padding: 18px 20px;
            border-bottom: 1px solid #edf2f7;
            color: #2c3e50;
            vertical-align: top;
            line-height: 1.45;
        }

        .endorsed-workers-table tbody tr:hover {
            background: #fcfdff;
        }

        .endorsed-workers-table tbody tr:last-child td {
            border-bottom: none;
        }

        .endorsed-workers-link {
            color: #674cbf;
            font-weight: 600;
            text-decoration: none;
        }

        .endorsed-workers-link:hover {
            text-decoration: underline;
        }

        .endorsed-workers-status {
            margin-bottom: 20px;
            padding: 16px 18px;
            border-radius: 12px;
            border: 1px solid #cde7d8;
            background: #eefaf3;
            color: #166534;
            font-weight: 600;
        }

        .endorsed-workers-error {
            margin-bottom: 20px;
            padding: 16px 18px;
            border-radius: 12px;
            border: 1px solid #f5c2c7;
            background: #fff5f5;
            color: #b42318;
            font-weight: 600;
        }

        .endorsed-workers-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .endorsed-workers-btn {
            border: none;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .endorsed-workers-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(31, 59, 91, 0.12);
        }

        .endorsed-workers-btn.primary {
            background: #674cbf;
            color: #fff;
        }

        .endorsed-workers-btn.secondary {
            background: #eff4ff;
            color: #29436f;
            border: 1px solid #d8e3f4;
        }

        .endorsed-workers-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1200;
            background: rgba(15, 23, 42, 0.45);
            padding: 24px;
            align-items: center;
            justify-content: center;
        }

        .endorsed-workers-modal.is-open {
            display: flex;
        }

        .endorsed-workers-modal-card {
            width: min(100%, 640px);
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.22);
            overflow: hidden;
        }

        .endorsed-workers-modal-header {
            padding: 22px 24px 14px;
            border-bottom: 1px solid #eef2f7;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .endorsed-workers-modal-header h2 {
            margin: 0 0 6px;
            font-size: 24px;
            color: #1f3b5b;
        }

        .endorsed-workers-modal-header p {
            margin: 0;
            color: #667085;
            font-size: 14px;
        }

        .endorsed-workers-close {
            border: none;
            background: #f8fafc;
            color: #475467;
            width: 36px;
            height: 36px;
            border-radius: 999px;
            font-size: 24px;
            line-height: 1;
            cursor: pointer;
        }

        .endorsed-workers-modal-body {
            padding: 24px;
        }

        .endorsed-workers-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .endorsed-workers-form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .endorsed-workers-form-group.full {
            grid-column: 1 / -1;
        }

        .endorsed-workers-form-group label {
            font-size: 14px;
            font-weight: 700;
            color: #344054;
        }

        .endorsed-workers-form-group input,
        .endorsed-workers-form-group textarea {
            width: 100%;
            border: 1px solid #d0d5dd;
            border-radius: 12px;
            padding: 12px 14px;
            font: inherit;
            color: #1f2937;
            background: #fff;
        }

        .endorsed-workers-form-group textarea {
            min-height: 110px;
            resize: vertical;
        }

        .endorsed-workers-link-row {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .endorsed-workers-link-row input {
            flex: 1;
        }

        .endorsed-workers-loading {
            display: none;
            position: absolute;
            right: 132px;
            top: 50%;
            transform: translateY(-50%);
            background: #fff;
            color: #674cbf;
            font-size: 12px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 999px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .endorsed-workers-modal-actions {
            margin-top: 22px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        @media (max-width: 768px) {
            .endorsed-workers-table th,
            .endorsed-workers-table td {
                padding: 14px 16px;
            }

            .endorsed-workers-form-grid {
                grid-template-columns: 1fr;
            }

            .endorsed-workers-link-row {
                flex-direction: column;
                align-items: stretch;
            }

            .endorsed-workers-loading {
                position: static;
                transform: none;
                align-self: flex-start;
            }

            .endorsed-workers-modal {
                padding: 16px;
            }
        }
    </style>

    <div class="dashboard-header">
        <h1>Endorsed Workers</h1>
    </div>

    @if(session('status'))
        <div class="endorsed-workers-status">{{ session('status') }}</div>
    @endif

    @if(session('error'))
        <div class="endorsed-workers-error">{{ session('error') }}</div>
    @endif

    @if($endorsements->isEmpty())
        <div class="endorsed-workers-card empty">
            <p>No endorsed workers have been recorded yet for your job postings.</p>
        </div>
    @else
        <div class="endorsed-workers-card">
            <div class="endorsed-workers-table-wrap">
            <table class="endorsed-workers-table">
                <thead>
                    <tr>
                        <th>Job Post</th>
                        <th>Worker</th>
                        <th>Meet & Greet Date</th>
                        <th>Meet & Greet Link</th>
                        <th>Endorsed By</th>
                        <th>Endorsed Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($endorsements as $endorsement)
                        <tr data-endorsement-id="{{ $endorsement->id }}"
                            data-worker-id="{{ $endorsement->worker->user_id ?? $endorsement->worker->id }}"
                            data-worker-name="{{ $endorsement->worker->fullname ?? $endorsement->worker->email ?? 'Worker' }}"
                            data-worker-email="{{ $endorsement->worker->email ?? '' }}"
                            data-meet-date="{{ optional($endorsement->meet_and_greet_date)->format('Y-m-d\\TH:i') }}"
                            data-meet-link="{{ $endorsement->meet_and_greet_link ?? '' }}">
                            <td>{{ $endorsement->jobPosting->title ?? 'N/A' }}</td>
                            <td><a href="javascript:void(0);" onclick="openWorkerProfileModal({{ $endorsement->worker->user_id ?? $endorsement->worker->id }})" class="endorsed-workers-link">{{ $endorsement->worker->fullname ?? $endorsement->worker->email ?? 'N/A' }}</a></td>
                            <td>{{ optional($endorsement->meet_and_greet_date)->format('M d, Y h:i A') ?? 'Not set' }}</td>
                            <td>
                                @if($endorsement->meet_and_greet_link)
                                    <a href="{{ $endorsement->meet_and_greet_link }}" target="_blank" rel="noopener" class="endorsed-workers-link">Open link</a>
                                @else
                                    Not set
                                @endif
                            </td>
                            <td>{{ $endorsement->admin->fullname ?? $endorsement->admin->email ?? 'Admin' }}</td>
                            <td>{{ $endorsement->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="endorsed-workers-actions">
                                    @if($endorsement->meet_and_greet_link)
                                        <button type="button" class="endorsed-workers-btn secondary" onclick="openMeetAndGreetModal('reschedule', {{ $endorsement->id }})">
                                            Reschedule
                                        </button>
                                    @else
                                        <button type="button" class="endorsed-workers-btn primary" onclick="openMeetAndGreetModal('schedule', {{ $endorsement->id }})">
                                            Schedule
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
    @endif
</div>

<div id="meetAndGreetModal" class="endorsed-workers-modal">
    <div class="endorsed-workers-modal-card">
        <div class="endorsed-workers-modal-header">
            <div>
                <h2 id="meetAndGreetModalTitle">Schedule Meet & Greet</h2>
                <p id="meetAndGreetModalSubtitle">Set the date, time, and meeting link for this endorsed worker.</p>
            </div>
            <button type="button" class="endorsed-workers-close" onclick="closeMeetAndGreetModal()">&times;</button>
        </div>

        <div class="endorsed-workers-modal-body">
            <form id="meetAndGreetForm" method="POST" action="{{ url('/client/endorsements/schedule') }}">
                @csrf
                <input type="hidden" name="_method" id="meetAndGreetMethod" value="POST">
                <input type="hidden" name="endorsement_id" id="meetAndGreetEndorsementId">
                <input type="hidden" name="meet_and_greet_date" id="meetAndGreetDate">

                <div class="endorsed-workers-form-grid">
                    <div class="endorsed-workers-form-group">
                        <label for="meetAndGreetStartDate">Start Date & Time</label>
                        <input type="datetime-local" id="meetAndGreetStartDate" required>
                    </div>

                    <div class="endorsed-workers-form-group">
                        <label for="meetAndGreetEndDate">End Date & Time</label>
                        <input type="datetime-local" id="meetAndGreetEndDate" required>
                    </div>

                    <div class="endorsed-workers-form-group full">
                        <label for="meetAndGreetLink">Location / Meet Link</label>
                        <div class="endorsed-workers-link-row">
                            <input type="text" id="meetAndGreetLink" name="meet_and_greet_link" placeholder="Enter the meet-and-greet link or location">
                            <button type="button" class="endorsed-workers-btn secondary" id="generateMeetAndGreetLinkBtn">Create Google Meet link</button>
                            <span id="meetAndGreetLoading" class="endorsed-workers-loading">Generating...</span>
                        </div>
                    </div>

                    <div class="endorsed-workers-form-group full">
                        <label for="meetAndGreetWorker">Worker</label>
                        <input type="text" id="meetAndGreetWorker" readonly>
                    </div>
                </div>

                <div class="endorsed-workers-modal-actions">
                    <button type="button" class="endorsed-workers-btn secondary" onclick="closeMeetAndGreetModal()">Cancel</button>
                    <button type="submit" class="endorsed-workers-btn primary" id="meetAndGreetSubmitBtn">Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const meetAndGreetModal = document.getElementById('meetAndGreetModal');
    const meetAndGreetForm = document.getElementById('meetAndGreetForm');
    const meetAndGreetMethod = document.getElementById('meetAndGreetMethod');
    const meetAndGreetEndorsementId = document.getElementById('meetAndGreetEndorsementId');
    const meetAndGreetDate = document.getElementById('meetAndGreetDate');
    const meetAndGreetStartDate = document.getElementById('meetAndGreetStartDate');
    const meetAndGreetEndDate = document.getElementById('meetAndGreetEndDate');
    const meetAndGreetLink = document.getElementById('meetAndGreetLink');
    const meetAndGreetWorker = document.getElementById('meetAndGreetWorker');
    const meetAndGreetModalTitle = document.getElementById('meetAndGreetModalTitle');
    const meetAndGreetModalSubtitle = document.getElementById('meetAndGreetModalSubtitle');
    const meetAndGreetSubmitBtn = document.getElementById('meetAndGreetSubmitBtn');
    const meetAndGreetLoading = document.getElementById('meetAndGreetLoading');

    function openMeetAndGreetModal(mode, endorsementId) {
        const row = document.querySelector(`tr[data-endorsement-id="${endorsementId}"]`);

        if (!row) {
            return;
        }

        const workerName = row.dataset.workerName || 'Worker';
        const existingDate = row.dataset.meetDate || '';
        const existingLink = row.dataset.meetLink || '';

        meetAndGreetEndorsementId.value = endorsementId;
        meetAndGreetWorker.value = workerName;
        meetAndGreetStartDate.value = existingDate;
        meetAndGreetEndDate.value = existingDate;
        meetAndGreetLink.value = existingLink;

        if (mode === 'reschedule') {
            meetAndGreetStartDate.value = '';
            meetAndGreetEndDate.value = '';
            meetAndGreetLink.value = '';
            meetAndGreetForm.action = "{{ url('/client/endorsements/reschedule') }}";
            meetAndGreetMethod.value = 'PUT';
            meetAndGreetModalTitle.textContent = 'Reschedule Meet & Greet';
            meetAndGreetModalSubtitle.textContent = 'Update the time or replace the existing meeting link for this worker.';
            meetAndGreetSubmitBtn.textContent = 'Reschedule';
        } else {
            meetAndGreetStartDate.value = existingDate;
            meetAndGreetEndDate.value = existingDate;
            meetAndGreetLink.value = existingLink;
            meetAndGreetForm.action = "{{ url('/client/endorsements/schedule') }}";
            meetAndGreetMethod.value = 'POST';
            meetAndGreetModalTitle.textContent = 'Schedule Meet & Greet';
            meetAndGreetModalSubtitle.textContent = 'Set the date, time, and meeting link for this endorsed worker.';
            meetAndGreetSubmitBtn.textContent = 'Schedule';
        }

        meetAndGreetModal.classList.add('is-open');
    }

    function closeMeetAndGreetModal() {
        meetAndGreetModal.classList.remove('is-open');
        meetAndGreetForm.reset();
        meetAndGreetMethod.value = 'POST';
        meetAndGreetForm.action = "{{ url('/client/endorsements/schedule') }}";
        meetAndGreetLoading.style.display = 'none';
    }

    document.getElementById('generateMeetAndGreetLinkBtn').addEventListener('click', function () {
        const endorsementId = meetAndGreetEndorsementId.value;
        const row = document.querySelector(`tr[data-endorsement-id="${endorsementId}"]`);
        const startDate = meetAndGreetStartDate.value;
        const endDate = meetAndGreetEndDate.value;

        if (!startDate || !endDate) {
            alert('Please set both the start and end date first.');
            return;
        }

        meetAndGreetLoading.style.display = 'inline';

        fetch('https://hook.us2.make.com/ey8o1k4wfsk04e8own7uy396am2x5fp9', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                interview_type: 'Meet & Greet',
                applicant_name: row ? row.dataset.workerName : '',
                applicant_email: row ? row.dataset.workerEmail : '',
                start_datetime: startDate,
                end_datetime: endDate,
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Webhook request failed');
            }

            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (error) {
                    return text;
                }
            });
        })
        .then(data => {
            if (typeof data === 'string' && data.startsWith('http')) {
                meetAndGreetLink.value = data;
            }
        })
        .catch(error => {
            console.error('Webhook error:', error);
            alert('Unable to generate the Google Meet link right now.');
        })
        .finally(() => {
            meetAndGreetLoading.style.display = 'none';
        });
    });

    meetAndGreetForm.addEventListener('submit', function () {
        meetAndGreetDate.value = meetAndGreetStartDate.value;
    });

    window.addEventListener('click', function (event) {
        if (event.target === meetAndGreetModal) {
            closeMeetAndGreetModal();
        }
    });
</script>

<!-- Worker Profile Modal -->
<div id="workerProfileModal" style="display: none; position: fixed; inset: 0; z-index: 1300; background: rgba(15, 23, 42, 0.5); padding: 24px; overflow-y: auto;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 24px;">
            <h2 style="margin: 0; color: #fff; font-size: 24px;">Worker Profile</h2>
            <button type="button" id="closeWorkerProfileModal" style="background: rgba(255, 255, 255, 0.2); border: none; color: #fff; width: 40px; height: 40px; border-radius: 999px; font-size: 24px; line-height: 1; cursor: pointer; display: grid; place-items: center; transition: background 0.2s ease;" onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'" onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'">&times;</button>
        </div>
        <div id="workerProfileContent" style="background: #f8fafc; border-radius: 24px; padding: 40px; max-height: calc(100vh - 140px); overflow-y: auto;">
            <!-- Loading spinner -->
            <div id="workerProfileLoading" style="display: none; text-align: center; padding: 60px 20px;">
                <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #e5e7eb; border-top-color: #6b46c1; border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
                <p style="margin-top: 16px; color: #6b7280; font-size: 16px;">Loading worker profile...</p>
            </div>
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<style>
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    #workerProfileModal {
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>

<script>
    const workerProfileModal = document.getElementById('workerProfileModal');
    const workerProfileContent = document.getElementById('workerProfileContent');
    const workerProfileLoading = document.getElementById('workerProfileLoading');

    function openWorkerProfileModal(workerId) {
        // Show modal with loading spinner
        workerProfileModal.style.display = 'block';
        workerProfileLoading.style.display = 'block';
        workerProfileContent.innerHTML = '';
        workerProfileContent.appendChild(workerProfileLoading);

        // Fetch worker profile
        fetch(`{{ url('/client/worker-profile') }}/${workerId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(html => {
                workerProfileLoading.style.display = 'none';
                workerProfileContent.innerHTML = html;

                // Scroll to top of modal
                workerProfileContent.scrollTop = 0;
            })
            .catch(error => {
                console.error('Error loading worker profile:', error);
                workerProfileLoading.style.display = 'none';
                workerProfileContent.innerHTML = `
                    <div style="text-align: center; padding: 60px 20px;">
                        <div style="color: #dc2626; font-size: 16px; margin-bottom: 12px;">Error loading worker profile</div>
                        <p style="color: #6b7280; margin: 0;">An error occurred while loading the worker profile. Please try again.</p>
                    </div>
                `;
            });
    }

    function closeWorkerProfileModal() {
        workerProfileModal.style.display = 'none';
        workerProfileContent.innerHTML = '';
        workerProfileLoading.style.display = 'none';
    }

    document.getElementById('closeWorkerProfileModal').addEventListener('click', closeWorkerProfileModal);

    // Close modal when clicking outside of content area
    window.addEventListener('click', function (event) {
        if (event.target === workerProfileModal) {
            closeWorkerProfileModal();
        }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && workerProfileModal.style.display === 'block') {
            closeWorkerProfileModal();
        }
    });
</script>
@endsection
