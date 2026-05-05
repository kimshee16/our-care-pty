@extends('layouts.dashboard')

@section('page-title', 'Finalization')

@php $routePrefix = session('user')['accounttype'] ?? 'client'; @endphp

@section('content')
<div class="dashboard-content">
    <div class="dashboard-header">
        <h1>Finalization</h1>
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
                .status-completed {
                    background: #d4edda;
                    color: #155724;
                }
                .status-rejected {
                    background: #f8d7da;
                    color: #721c24;
                }
                .status-hired {
                    background: #d1ecf1;
                    color: #0c5460;
                }
                .decision-pending {
                    background: #fff3cd;
                    color: #856404;
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
                }
                .btn-action:hover {
                    opacity: 0.8;
                    transform: scale(1.05);
                }
                .btn-action.btn-success {
                    background: #28a745;
                }
                .btn-action.btn-warning {
                    background: #d39e00;
                }
                .btn-action.btn-danger {
                    background: #dc3545;
                }
                .btn-action.btn-info {
                    background: #0f7c90;
                }
                .action-group {
                    display: flex;
                    gap: 8px;
                    align-items: center;
                }
                .action-group form {
                    margin: 0;
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
            </style>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Applicant Name</th>
                        <th>Email Address</th>
                        <th>Job Post Title</th>
                        <th>Interview Schedule</th>
                        <th>Interview Status</th>
                        <th>Final Decision</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($interviews as $application)
                    <tr>
                        <td>{{ $application->applicant?->fullname ?? 'N/A' }}</td>
                        <td><a href="mailto:{{ $application->applicant?->email ?? '' }}">{{ $application->applicant?->email ?? 'N/A' }}</a></td>
                        <td>{{ $application->jobPosting->title ?? 'N/A' }}</td>
                        <td>{{ $application->interview_date ? date('M d, Y H:i', strtotime($application->interview_date)) : 'N/A' }}</td>
                        <td>
                            @if($application->interview_status == 'completed')
                                <span class="status-badge status-completed">Completed</span>
                            @elseif($application->interview_status == 'rejected')
                                <span class="status-badge status-rejected">Rejected</span>
                            @elseif($application->interview_status == 'hired')
                                <span class="status-badge status-hired">Hired</span>
                            @else
                                <span class="status-badge">{{ ucfirst($application->interview_status) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($application->interview_status == 'hired')
                                <span class="status-badge status-hired">Hired</span>
                            @elseif($application->interview_status == 'rejected')
                                <span class="status-badge status-rejected">Rejected</span>
                            @else
                                <span class="status-badge decision-pending">Pending Decision</span>
                            @endif
                        </td>
                        <td>
                            @if($application->interview_status == 'completed')
                                <div class="action-group">
                                    <form method="POST" action="{{ url('/' . $routePrefix . '/interviews/hire/' . $application->id) }}" onsubmit="return confirm('Are you sure you want to mark this applicant as hired?');">
                                        @csrf
                                        <button type="submit" class="btn-action btn-success" title="Hire Applicant">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ url('/' . $routePrefix . '/interviews/reject/' . $application->id) }}" onsubmit="return confirm('Are you sure you want to reject this applicant? A rejection email will be sent to the applicant once you confirm.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-danger" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No interviews ready for final decision.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
