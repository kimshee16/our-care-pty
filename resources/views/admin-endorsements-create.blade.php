@extends('layouts.dashboard')

@section('page-title', 'Create Endorsement')

@section('content')
<div class="dashboard-content">
    <div class="dashboard-header">
        <h1>Create Endorsement</h1>
    </div>

    @if(session('success'))
        <div style="background: #ecfdf5; color: #065f46; padding: 16px; border-radius: 12px; border: 1px solid #a7f3d0; margin-bottom: 24px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fef2f2; color: #991b1b; padding: 16px; border-radius: 12px; border: 1px solid #fecaca; margin-bottom: 24px;">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #fef2f2; color: #991b1b; padding: 16px; border-radius: 12px; border: 1px solid #fecaca; margin-bottom: 24px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="background: white; border-radius: 24px; padding: 32px; box-shadow: 0 25px 60px rgba(15, 23, 42, 0.08); width: 100%; max-width: 100%; box-sizing: border-box;">
        <form method="POST" action="{{ url('/admin/endorsements') }}">
            @csrf

            <div style="display: grid; gap: 20px;">
                <div style="display: grid; gap: 12px;">
                    <label for="applicationSearch" style="font-weight: 700; color: #111827;">Job Application</label>
                    <input id="applicationSearch" type="text" placeholder="Search by job title or worker name" style="width: 100%; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: #f8fafc; color: #111827; font-size: 15px;">
                </div>

                <div style="display: grid; gap: 12px;">
                    <label for="applicationSelect" style="font-weight: 700; color: #111827;">Select application</label>
                    <select id="applicationSelect" name="application_id" required style="width: 100%; padding: 14px 16px; border: 1px solid #e5e7eb; border-radius: 14px; background: #ffffff; color: #111827; font-size: 15px;">
                        <option value="">Choose job application</option>
                        @foreach($applications as $application)
                            <option value="{{ $application->id }}">
                                {{ $application->jobPosting->title ?? 'Untitled job' }} - {{ optional($application->applicant)->fullname ?? optional($application->applicant)->email ?? 'Worker' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" style="background: #6b46c1; color: white; border: none; padding: 14px 28px; border-radius: 14px; font-weight: 700; font-size: 15px; cursor: pointer;">Create Endorsement</button>
                </div>
            </div>
        </form>
    </div>

    <div style="background: white; border-radius: 24px; padding: 32px; box-shadow: 0 25px 60px rgba(15, 23, 42, 0.08); margin-top: 24px; width: 100%; max-width: 100%; min-height: 280px; box-sizing: border-box;">
        <h2 style="margin-top: 0; margin-bottom: 20px; color: #111827; font-size: 22px;">Created Endorsements</h2>
        @if($endorsements->isEmpty())
            <div style="padding: 24px; border: 1px solid #e5e7eb; border-radius: 16px; color: #6b7280;">No endorsements created yet.</div>
        @else
            <div style="overflow-x: auto; padding-bottom: 4px; width: 100%;">
                <table class="endorsement-table" style="width: 100%; border-collapse: collapse; font-size: 14px; table-layout: auto;">
                    <thead style="background: #f8fafc; text-align: left;">
                        <tr>
                            <th style="padding: 14px 16px; color: #6b7280; text-transform: uppercase; font-size: 12px; width: 32%;">Job</th>
                            <th style="padding: 14px 16px; color: #6b7280; text-transform: uppercase; font-size: 12px; width: 18%;">Worker</th>
                            <th style="padding: 14px 16px; color: #6b7280; text-transform: uppercase; font-size: 12px; width: 18%;">Client</th>
                            <th style="padding: 14px 16px; color: #6b7280; text-transform: uppercase; font-size: 12px; width: 18%;">Endorsed By</th>
                            <th style="padding: 14px 16px; color: #6b7280; text-transform: uppercase; font-size: 12px; width: 14%;">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($endorsements as $endorsement)
                            <tr style="border-top: 1px solid #e5e7eb; background: white;">
                                <td style="padding: 14px 16px; vertical-align: top; color: #111827; word-break: break-word;">{{ $endorsement->jobPosting->title ?? 'N/A' }}</td>
                                <td style="padding: 14px 16px; vertical-align: top; color: #111827; word-break: break-word;">{{ optional($endorsement->worker)->fullname ?? optional($endorsement->worker)->email ?? 'N/A' }}</td>
                                <td style="padding: 14px 16px; vertical-align: top; color: #111827; word-break: break-word;">{{ optional($endorsement->client)->business_name ?? optional($endorsement->client)->name ?? 'N/A' }}</td>
                                <td style="padding: 14px 16px; vertical-align: top; color: #111827; word-break: break-word;">{{ optional($endorsement->admin)->fullname ?? optional($endorsement->admin)->email ?? 'Admin' }}</td>
                                <td style="padding: 14px 16px; vertical-align: top; color: #6b7280; white-space: nowrap;">{{ $endorsement->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<style>
    .endorsement-table tbody tr:hover {
        background: #f9fafb;
    }
    .endorsement-table th,
    .endorsement-table td {
        transition: background 0.2s ease;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('applicationSearch');
        const applicationSelect = document.getElementById('applicationSelect');

        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase();
            Array.from(applicationSelect.options).forEach(option => {
                if (!option.value) {
                    option.style.display = '';
                    return;
                }

                const text = option.text.toLowerCase();
                option.style.display = text.includes(query) ? '' : 'none';
            });
        });
    });
</script>
@endsection
