@extends('layouts.dashboard')

@section('page-title', 'My Jobs')

@section('content')
<div class="dashboard-content">
    <div class="dashboard-header">
        <h1>My Job Posts</h1>
    </div>

    @if(session('status'))
        <div style="background: #d1fae5; color: #064e3b; padding: 16px; border-radius: 10px; border: 1px solid #a7f3d0; margin-bottom: 20px;">
            {{ session('status') }}
        </div>
    @endif

    @php
        $sessionUser = session('user', []);
        $clientApproved = ($sessionUser['accounttype'] ?? '') === 'client' && ($sessionUser['approved'] ?? 0) == 1;
    @endphp
    @if($clientApproved)
        <div style="display: flex; justify-content: flex-end; margin-bottom: 24px; gap: 16px; flex-wrap: wrap;">
            <a href="/client/job-postings/create" style="padding: 12px 20px; border-radius: 10px; background: var(--accent); color: white; font-weight: 600; text-decoration: none;">Post New Job</a>
        </div>
    @endif

    @if($jobs->isEmpty())
        <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); border: 1px solid #e9ecef; text-align: center;">
            <p style="margin: 0; color: #6b7280;">You haven't posted any jobs yet. Start by creating a new job post.</p>
        </div>
    @else
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 760px;">
                <thead>
                    <tr style="background: #f3f4f6; text-align: left;">
                        <th style="padding: 14px 12px; font-weight: 600; color: #374151;">Title</th>
                        <th style="padding: 14px 12px; font-weight: 600; color: #374151;">Location</th>
                        <th style="padding: 14px 12px; font-weight: 600; color: #374151;">Salary</th>
                        <th style="padding: 14px 12px; font-weight: 600; color: #374151;">Created</th>
                        <th style="padding: 14px 12px; font-weight: 600; color: #374151;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jobs as $job)
                        <tr style="border-top: 1px solid #e5e7eb;">
                            <td style="padding: 14px 12px; vertical-align: top;">
                                <div style="font-weight: 600; color: #111827;">{{ $job->title }}</div>
                                <div style="font-size: 13px; color: #6b7280; margin-top: 4px;">{{ \Illuminate\Support\Str::limit($job->description, 100) }}</div>
                            </td>
                            <td style="padding: 14px 12px; vertical-align: top; color: #374151;">{{ $job->location ?? '-' }}</td>
                            <td style="padding: 14px 12px; vertical-align: top; color: #374151;">
                                @if($job->minimum_pay_offer && $job->maximum_pay_offer)
                                    ${{ number_format($job->minimum_pay_offer, 0) }} - ${{ number_format($job->maximum_pay_offer, 0) }}
                                @elseif($job->minimum_pay_offer)
                                    ${{ number_format($job->minimum_pay_offer, 0) }}
                                @elseif($job->maximum_pay_offer)
                                    ${{ number_format($job->maximum_pay_offer, 0) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td style="padding: 14px 12px; vertical-align: top; color: #6b7280;">{{ $job->created_at->diffForHumans() }}</td>
                            <td style="padding: 14px 12px; vertical-align: top;">
                                <a href="/client/job-postings/{{ $job->id }}/edit" style="font-weight: 600; color: var(--accent); margin-right: 12px;">Edit</a>
                                <form action="/client/job-postings/{{ $job->id }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this job post?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: transparent; border: none; color: #dc2626; font-weight: 600; cursor: pointer; padding: 0;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
