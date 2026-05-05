@extends('layouts.dashboard')

@section('page-title', 'Client Dashboard')

@section('content')
<div class="dashboard-content">
    <div class="dashboard-header">
        <h1>Welcome to Your Dashboard</h1>
    </div>

    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon">📋</div>
            <div class="stat-info">
                <h3>Active Jobs</h3>
                <p class="stat-number">{{ $activeJobs ?? 0 }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <h3>Applications</h3>
                <p class="stat-number">{{ $applications ?? 0 }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📅</div>
            <div class="stat-info">
                <h3>Met and Greeted</h3>
                <p class="stat-number">{{ $interviews ?? 0 }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-info">
                <h3>Endorsed</h3>
                <p class="stat-number">{{ $endorsedWorkers ?? 0 }}</p>
            </div>
        </div>
    </div>

    @php
        $sessionUser = session('user', []);
        $clientApproved = ($sessionUser['accounttype'] ?? '') === 'client' && ($sessionUser['approved'] ?? 0) == 1;
    @endphp
    <div class="dashboard-actions">
        <a href="/client/job-postings" class="action-btn primary">My Jobs</a>
        <a href="/client/endorsed-workers" class="action-btn secondary">Endorsed Workers</a>
        @if($clientApproved)
            <a href="/client/job-postings/create" class="action-btn secondary">Post New Job</a>
        @endif
    </div>
</div>
@endsection
