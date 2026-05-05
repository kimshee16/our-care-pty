@extends('layouts.dashboard')

@section('page-title', 'Settings')

@section('content')
<div class="dashboard-content">
    <div class="dashboard-header">
        <h1>Settings</h1>
    </div>

    @if(session('status'))
        <div style="background: #ecfdf5; color: #047857; padding: 16px; border-radius: 12px; border: 1px solid #bbf7d0; margin-bottom: 24px;">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #fef2f2; color: #b91c1c; padding: 16px; border-radius: 12px; border: 1px solid #fecaca; margin-bottom: 24px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: grid; gap: 24px; grid-template-columns: 360px 1fr; align-items: start;">
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <h2 style="margin: 0 0 18px 0; color: #111827; font-size: 20px;">Add NDIS Requirement</h2>
            <form method="POST" action="{{ url('/admin/settings/ndis-requirements') }}" style="display: grid; gap: 14px;">
                @csrf
                <label style="display: grid; gap: 8px; color: #111827; font-weight: 600;">
                    Requirement
                    <input type="text" name="requirements" value="{{ old('requirements') }}" required maxlength="255" placeholder="e.g. Worker Screening Check" style="width: 100%; padding: 12px 14px; border: 1px solid #e5e7eb; border-radius: 8px; background: #f8fafc; color: #111827;">
                </label>
                <button type="submit" style="background: var(--accent); color: white; border: none; border-radius: 8px; padding: 12px 16px; font-weight: 700; cursor: pointer;">Add Requirement</button>
            </form>
        </div>

        <div style="background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow: hidden;">
            <div style="padding: 20px 24px; border-bottom: 1px solid #e5e7eb;">
                <h2 style="margin: 0; color: #111827; font-size: 20px;">NDIS Requirements</h2>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #f9fafb;">
                        <tr>
                            <th style="padding: 14px 16px; text-align: left; color: var(--accent); font-size: 13px; text-transform: uppercase;">ID</th>
                            <th style="padding: 14px 16px; text-align: left; color: var(--accent); font-size: 13px; text-transform: uppercase;">Requirement</th>
                            <th style="padding: 14px 16px; text-align: left; color: var(--accent); font-size: 13px; text-transform: uppercase;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requirements as $requirement)
                            <tr style="border-top: 1px solid #f3f4f6;">
                                <td style="padding: 14px 16px; color: #374151;">#{{ $requirement->id }}</td>
                                <td style="padding: 14px 16px; color: #111827; font-weight: 600;">{{ $requirement->requirements }}</td>
                                <td style="padding: 14px 16px;">
                                    <form method="POST" action="{{ url('/admin/settings/ndis-requirements/' . $requirement->id) }}" onsubmit="return confirm('Remove this NDIS requirement? Existing worker completion links for this requirement will also be removed.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: #dc3545; color: white; border: none; border-radius: 8px; padding: 8px 12px; font-weight: 700; cursor: pointer;">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="padding: 32px 16px; text-align: center; color: #6b7280;">No NDIS requirements have been added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 900px) {
        .dashboard-content > div[style*="grid-template-columns: 360px 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endsection
