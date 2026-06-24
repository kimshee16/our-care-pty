<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'index');

// Local-only browser link for checking the configured mail transport.
Route::get('/test-email', [App\Http\Controllers\HealthcareController::class, 'sendTestEmail'])
    ->name('test-email');

// static pages converted from standalone HTML
// use AuthController to handle login form display and submission
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
Route::view('/signup-option', 'signup-option');
Route::view('/settings', 'settings')->middleware('auth');
Route::get('/profile-photos/{filename}', [App\Http\Controllers\HealthcareController::class, 'profilePhoto'])
    ->where('filename', '[A-Za-z0-9._-]+')
    ->name('profile-photos.show');
Route::get('/client-register', [App\Http\Controllers\ClientController::class, 'showRegistrationForm']);
Route::post('/client-register', [App\Http\Controllers\ClientController::class, 'register']);
Route::get('/dashboard', function () {
    if (auth()->check()) {
        $user = auth()->user();
        switch ($user->accounttype) {
            case 'admin':
                return redirect('/admin-registrations');
            case 'healthcare_worker':
                return redirect('/healthcare-jobs');
            case 'client':
            default:
                return view('client-dashboard');
        }
    }
    return redirect('/login');
})->middleware('auth');

Route::get('/client-dashboard', [App\Http\Controllers\ClientController::class, 'dashboard'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::get('/client/applications', [App\Http\Controllers\ClientController::class, 'applications'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::get('/client/applications/{applicationId}/download', [App\Http\Controllers\ClientController::class, 'downloadAttachment'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::get('/client/job-postings', [App\Http\Controllers\JobPostingController::class, 'index'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::get('/client/endorsed-workers', [App\Http\Controllers\ClientController::class, 'endorsedWorkers'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::get('/client/worker-profile/{workerId}', [App\Http\Controllers\ClientController::class, 'viewWorkerProfile'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::post('/client/endorsements/schedule', [App\Http\Controllers\ClientController::class, 'scheduleEndorsementMeetAndGreet'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::put('/client/endorsements/reschedule', [App\Http\Controllers\ClientController::class, 'rescheduleEndorsementMeetAndGreet'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::get('/client/job-postings/create', [App\Http\Controllers\JobPostingController::class, 'create'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::post('/client/job-postings', [App\Http\Controllers\JobPostingController::class, 'store'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::get('/client/job-postings/{id}/edit', [App\Http\Controllers\JobPostingController::class, 'edit'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::put('/client/job-postings/{id}', [App\Http\Controllers\JobPostingController::class, 'update'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::delete('/client/job-postings/{id}', [App\Http\Controllers\JobPostingController::class, 'destroy'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client', \App\Http\Middleware\EnsureEmailIsVerified::class);

// client interview routes
Route::get('/client/interviews', [App\Http\Controllers\ClientController::class, 'interviews'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

// New interview entry creation (no existing application ID required)
Route::post('/client/interviews', [App\Http\Controllers\ClientController::class, 'createInterview'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::post('/client/interviews/schedule', [App\Http\Controllers\ClientController::class, 'scheduleInterview'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::put('/client/interviews/reschedule', [App\Http\Controllers\ClientController::class, 'rescheduleInterview'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::post('/client/interviews/addNotes', [App\Http\Controllers\ClientController::class, 'addInterviewNotes'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::delete('/client/interviews/reject/{id}', [App\Http\Controllers\ClientController::class, 'rejectApplication'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::post('/client/test-email', [App\Http\Controllers\ClientController::class, 'sendTestEmail'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::post('/client/interviews/complete/{id}', [App\Http\Controllers\ClientController::class, 'completeInterview'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::post('/client/interviews/hire/{id}', [App\Http\Controllers\ClientController::class, 'hireApplicant'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::get('/client/interviews/{id}', [App\Http\Controllers\ClientController::class, 'interviewDetails'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

// Admin access for application and interview management
Route::get('/admin/applications', [App\Http\Controllers\ClientController::class, 'applications'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::get('/admin/applications/{applicationId}/download', [App\Http\Controllers\ClientController::class, 'downloadAttachment'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::get('/admin/interviews', [App\Http\Controllers\ClientController::class, 'interviews'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::post('/admin/interviews', [App\Http\Controllers\ClientController::class, 'createInterview'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::post('/admin/interviews/schedule', [App\Http\Controllers\ClientController::class, 'scheduleInterview'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::put('/admin/interviews/reschedule', [App\Http\Controllers\ClientController::class, 'rescheduleInterview'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::post('/admin/interviews/addNotes', [App\Http\Controllers\ClientController::class, 'addInterviewNotes'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::delete('/admin/interviews/reject/{id}', [App\Http\Controllers\ClientController::class, 'rejectApplication'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::post('/admin/interviews/complete/{id}', [App\Http\Controllers\ClientController::class, 'completeInterview'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::post('/admin/interviews/hire/{id}', [App\Http\Controllers\ClientController::class, 'hireApplicant'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::get('/admin/interviews/{id}', [App\Http\Controllers\ClientController::class, 'interviewDetails'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::get('/admin/finalization', [App\Http\Controllers\ClientController::class, 'finalization'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::get('/admin/endorsements/create', [App\Http\Controllers\ClientController::class, 'createEndorsement'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::post('/admin/endorsements', [App\Http\Controllers\ClientController::class, 'storeEndorsement'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::get('/admin/settings', [App\Http\Controllers\AdminController::class, 'settings'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::post('/admin/settings/ndis-requirements', [App\Http\Controllers\AdminController::class, 'storeRequirement'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::delete('/admin/settings/ndis-requirements/{id}', [App\Http\Controllers\AdminController::class, 'destroyRequirement'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

// healthcare registration handled by controller so that it creates both user and profile
Route::get('/healthcare-register', [App\Http\Controllers\HealthcareController::class, 'showRegistrationForm']);
Route::post('/healthcare-register', [App\Http\Controllers\HealthcareController::class, 'register']);
Route::get('/healthcare-jobs', [App\Http\Controllers\HealthcareController::class, 'jobs'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':healthcare_worker', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::get('/healthcare-jobs-details/{id}', [App\Http\Controllers\HealthcareController::class, 'jobDetails'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':healthcare_worker', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::post('/job-postings/{id}/apply', [App\Http\Controllers\HealthcareController::class, 'apply'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':healthcare_worker', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::get('/applications', [App\Http\Controllers\HealthcareController::class, 'applications'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':healthcare_worker', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::get('/applications/{applicationId}/download', [App\Http\Controllers\HealthcareController::class, 'downloadAttachment'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':healthcare_worker', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::put('/applications/{applicationId}', [App\Http\Controllers\HealthcareController::class, 'updateApplication'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':healthcare_worker', \App\Http\Middleware\EnsureEmailIsVerified::class);

Route::delete('/applications/{applicationId}', [App\Http\Controllers\HealthcareController::class, 'destroyApplication'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':healthcare_worker', \App\Http\Middleware\EnsureEmailIsVerified::class);

// profile page for healthcare workers
Route::get('/healthcare-profile', [App\Http\Controllers\HealthcareController::class, 'profile'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':healthcare_worker', \App\Http\Middleware\EnsureEmailIsVerified::class);
Route::post('/healthcare-profile', [App\Http\Controllers\HealthcareController::class, 'update'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':healthcare_worker', \App\Http\Middleware\EnsureEmailIsVerified::class);
Route::post('/healthcare-profile/photo', [App\Http\Controllers\HealthcareController::class, 'updateProfilePhoto'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':healthcare_worker', \App\Http\Middleware\EnsureEmailIsVerified::class);
Route::post('/healthcare-profile/skills', [App\Http\Controllers\HealthcareController::class, 'updateSkills'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':healthcare_worker', \App\Http\Middleware\EnsureEmailIsVerified::class);
Route::get('/admin-registrations', [App\Http\Controllers\AdminController::class, 'registrations'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');
Route::post('/admin-registrations/{id}/approve', [App\Http\Controllers\AdminController::class, 'approve'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');
Route::post('/admin-registrations/{id}/reject', [App\Http\Controllers\AdminController::class, 'reject'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');
    
// Finalization page for client
Route::get('/client/finalization', [App\Http\Controllers\ClientController::class, 'finalization'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin', \App\Http\Middleware\EnsureEmailIsVerified::class);

// email verification route
Route::get('/email/verify/{id}', function (\Illuminate\Http\Request $request, $id) {
    if (! $request->hasValidSignature()) {
        abort(403);
    }
    $user = App\Models\User::findOrFail($id);
    $user->verified = true;
    $user->email_verified_at = now();
    $user->save();
    return redirect('/login')->with('status', 'Email verified, you may now log in.');
})->name('verification.verify');

// Email verification pending page
Route::view('/email-verification-pending', 'email-verification-pending');
