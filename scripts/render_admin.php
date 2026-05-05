<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// build registrations same as controller
$users = \App\Models\User::with(['healthcareWorker'])
    ->where('accounttype', '!=', 'admin')
    ->get();

$registrations = [];
foreach ($users as $user) {
    $registration = [
        'id' => $user->id,
        'name' => $user->fullname,
        'type' => $user->accounttype === 'healthcare_worker' ? 'Healthcare Worker' : 'Client',
        'email' => $user->email,
        'phone' => $user->phone ?? 'N/A',
        'status' => $user->approved == 1 ? 'approved' : ($user->approved == -1 ? 'rejected' : 'pending'),
        'registered' => $user->created_at->format('Y-m-d'),
    ];
    if ($user->accounttype === 'healthcare_worker' && $user->healthcareWorker) {
        $hw = $user->healthcareWorker;
        $registration = array_merge($registration, [
            'profession' => $hw->profession ?? 'N/A',
            'specialization' => $hw->specialization ?? 'N/A',
            'licenseNumber' => $hw->license_number ?? 'N/A',
            'experience' => $hw->experience_years ?? 'N/A',
            'facility' => $hw->facility_name ?? 'N/A',
            'facilityAddress' => $hw->facility_address ?? 'N/A',
            'city' => $hw->city ?? 'N/A',
            'state' => $hw->state ?? 'N/A',
            'credentials' => $hw->credentials ?? 'N/A',
        ]);
    } else {
        $registration = array_merge($registration, [
            'dob' => 'N/A',
            'address' => 'N/A',
            'city' => 'N/A',
            'state' => 'N/A',
            'zipcode' => 'N/A',
            'country' => 'N/A',
        ]);
    }
    $registrations[] = $registration;
}

$html = view('admin-registrations', ['registrations' => $registrations])->render();
file_put_contents('rendered_admin.html', $html);
echo "Rendered to rendered_admin.html\n";
