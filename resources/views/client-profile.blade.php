@extends('layouts.dashboard')

@section('page-title', 'Client Profile')

@section('content')
@php
    $firstName = old('first_name', optional($client)->first_name ?: strtok($user->fullname ?? '', ' '));
    $lastName = old('last_name', optional($client)->last_name ?: trim(str_replace($firstName ?? '', '', $user->fullname ?? '')));
    $dateOfBirth = old('date_of_birth', optional($client)->date_of_birth
        ? \Carbon\Carbon::parse($client->date_of_birth)->format('Y-m-d')
        : '');
@endphp

<div class="dashboard-content">
    <div class="dashboard-header">
        <h1>Client Profile</h1>
    </div>

    @if(session('status'))
        <div style="margin-bottom: 20px; padding: 14px 16px; border-radius: 8px; background: #ecfdf5; color: #047857; border: 1px solid #bbf7d0; font-weight: 700;">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div style="margin-bottom: 20px; padding: 14px 16px; border-radius: 8px; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; font-weight: 700;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ url('/profile') }}" style="display: grid; gap: 22px; max-width: 920px; padding: 24px; border-radius: 12px; background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
        @csrf

        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px;">
            <label style="display: grid; gap: 8px; font-weight: 700;">
                First Name
                <input type="text" name="first_name" value="{{ $firstName }}" required style="padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
            </label>
            <label style="display: grid; gap: 8px; font-weight: 700;">
                Last Name
                <input type="text" name="last_name" value="{{ $lastName }}" required style="padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
            </label>
        </div>

        <label style="display: grid; gap: 8px; font-weight: 700;">
            Display Name
            <input type="text" name="alias" value="{{ old('alias', optional($client)->alias) }}" style="padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
        </label>

        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px;">
            <label style="display: grid; gap: 8px; font-weight: 700;">
                Email
                <input type="email" name="email" value="{{ old('email', optional($client)->email ?: $user->email) }}" required style="padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
            </label>
            <label style="display: grid; gap: 8px; font-weight: 700;">
                Phone
                <input type="text" name="phone" value="{{ old('phone', optional($client)->phone ?: $user->phone) }}" required style="padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
            </label>
        </div>

        <label style="display: grid; gap: 8px; font-weight: 700;">
            Date of Birth
            <input type="date" name="date_of_birth" value="{{ $dateOfBirth }}" required style="padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
        </label>

        <label style="display: grid; gap: 8px; font-weight: 700;">
            Address
            <input type="text" name="address" value="{{ old('address', optional($client)->address) }}" required style="padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
        </label>

        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px;">
            <label style="display: grid; gap: 8px; font-weight: 700;">
                City
                <input type="text" name="city" value="{{ old('city', optional($client)->city) }}" required style="padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
            </label>
            <label style="display: grid; gap: 8px; font-weight: 700;">
                State
                <input type="text" name="state" value="{{ old('state', optional($client)->state) }}" required style="padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
            </label>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px;">
            <label style="display: grid; gap: 8px; font-weight: 700;">
                Zip Code
                <input type="text" name="zip_code" value="{{ old('zip_code', optional($client)->zip_code) }}" required style="padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
            </label>
            <label style="display: grid; gap: 8px; font-weight: 700;">
                Country
                <input type="text" name="country" value="{{ old('country', optional($client)->country) }}" required style="padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
            </label>
        </div>

        <div style="display: flex; justify-content: flex-end;">
            <button type="submit" style="padding: 12px 18px; border: 0; border-radius: 8px; background: var(--accent); color: #fff; font-weight: 800; cursor: pointer;">Save Profile</button>
        </div>
    </form>
</div>
@endsection
