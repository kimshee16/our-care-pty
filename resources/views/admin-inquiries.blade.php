@extends('layouts.dashboard')

@section('page-title', 'Inquiries')

@section('content')
<div class="dashboard-content">
    <div class="dashboard-header">
        <h1>Inquiries</h1>
    </div>

    <div class="inquiry-list">
        @forelse ($inquiries as $inquiry)
            <article class="inquiry-entry">
                <header>
                    <h2>{{ $inquiry->name }}</h2>
                    <time datetime="{{ $inquiry->created_at->toIso8601String() }}">{{ $inquiry->created_at->format('j M Y, H:i T') }}</time>
                </header>

                <dl>
                    <div>
                        <dt>Email</dt>
                        <dd><a href="mailto:{{ rawurlencode($inquiry->email) }}">{{ $inquiry->email }}</a></dd>
                    </div>
                    <div>
                        <dt>Phone</dt>
                        <dd>{{ $inquiry->phone ?? 'Not provided' }}</dd>
                    </div>
                </dl>

                <p class="inquiry-message">{{ $inquiry->message }}</p>
            </article>
        @empty
            <p>No inquiries have been submitted yet.</p>
        @endforelse
    </div>

    @if ($inquiries->hasPages())
        <nav class="inquiry-pagination" aria-label="Inquiry pages">
            @if ($inquiries->previousPageUrl())
                <a href="{{ $inquiries->previousPageUrl() }}">Previous page</a>
            @endif
            @if ($inquiries->nextPageUrl())
                <a href="{{ $inquiries->nextPageUrl() }}">Next page</a>
            @endif
        </nav>
    @endif
</div>

<style>
    .inquiry-list { display: grid; gap: 16px; }
    .inquiry-entry {
        padding: 20px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #fff;
        overflow-wrap: anywhere;
    }
    .inquiry-entry header, .inquiry-entry dl, .inquiry-pagination {
        display: flex;
        flex-wrap: wrap;
        gap: 12px 28px;
    }
    .inquiry-entry header { align-items: baseline; justify-content: space-between; }
    .inquiry-entry h2 { margin: 0; font-size: 1.125rem; }
    .inquiry-entry time { color: #475569; font-size: 0.875rem; }
    .inquiry-entry dl { margin: 14px 0; }
    .inquiry-entry dt { font-weight: 600; }
    .inquiry-entry dd { margin: 4px 0 0; }
    .inquiry-message { margin: 0; white-space: pre-wrap; }
    .inquiry-pagination { margin-top: 20px; }
    .inquiry-entry a, .inquiry-pagination a { color: #1d4ed8; text-decoration: underline; }
</style>
@endsection
