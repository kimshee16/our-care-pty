<?php

use Illuminate\Support\Facades\Route;

Route::prefix('admin/file-manager')->name('file-manager.')
    ->middleware(\App\Http\Middleware\EnsureFileManagerAdmin::class)
    ->controller(App\Http\Controllers\FileManagerController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/folders', 'createFolder')->name('folders');
        Route::post('/upload', 'upload')->name('upload');
        Route::patch('/files/{file}', 'update')->name('update');
        Route::delete('/files/{file}', 'trash')->name('trash');
        Route::get('/files/{file}/download', 'download')->name('download');
    });

Route::get('/', [App\Http\Controllers\CmsController::class, 'home'])->name('home.v2');
Route::get('/home-v2', [App\Http\Controllers\CmsController::class, 'home']);
Route::get('/about', [App\Http\Controllers\CmsController::class, 'about'])->name('about.v2');
Route::get('/about-v2', [App\Http\Controllers\CmsController::class, 'about']);
Route::get('/services', [App\Http\Controllers\CmsController::class, 'services'])->name('services.v2');
Route::get('/services-v2', [App\Http\Controllers\CmsController::class, 'services']);
Route::get('/onboarding', [App\Http\Controllers\CmsController::class, 'onboarding'])->name('onboarding.v2');
Route::get('/onboarding-v2', [App\Http\Controllers\CmsController::class, 'onboarding']);
Route::get('/intake', [App\Http\Controllers\CmsController::class, 'intake'])->name('intake.v2');
Route::get('/intake-v2', [App\Http\Controllers\CmsController::class, 'intake']);
Route::get('/contact', [App\Http\Controllers\CmsController::class, 'contact'])->name('contact.v2');
Route::get('/contact-v2', [App\Http\Controllers\CmsController::class, 'contact']);
Route::get('/services/{service}', [App\Http\Controllers\CmsController::class, 'serviceDetail'])->name('services.detail.v2');
Route::get('/services-v2/{service}', [App\Http\Controllers\CmsController::class, 'serviceDetail']);
Route::get('/cms/home', [App\Http\Controllers\CmsController::class, 'home']);
Route::get('/cms/about', [App\Http\Controllers\CmsController::class, 'about']);
Route::get('/cms/services', [App\Http\Controllers\CmsController::class, 'services']);
Route::get('/cms/onboarding', [App\Http\Controllers\CmsController::class, 'onboarding']);
Route::get('/cms/intake', [App\Http\Controllers\CmsController::class, 'intake']);
Route::get('/cms/contact', [App\Http\Controllers\CmsController::class, 'contact']);
Route::get('/cms/services/{service}', [App\Http\Controllers\CmsController::class, 'serviceDetail']);

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
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client');

Route::get('/profile', [App\Http\Controllers\ClientController::class, 'profile'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client');

Route::post('/profile', [App\Http\Controllers\ClientController::class, 'updateProfile'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client');

Route::get('/client/applications', [App\Http\Controllers\ClientController::class, 'applications'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::get('/client/applications/{applicationId}/download', [App\Http\Controllers\ClientController::class, 'downloadAttachment'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::get('/client/job-postings', [App\Http\Controllers\JobPostingController::class, 'index'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client');

Route::get('/client/endorsed-workers', [App\Http\Controllers\ClientController::class, 'endorsedWorkers'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client');

Route::get('/client/document-signing', [App\Http\Controllers\DocumentSigningController::class, 'index'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client');

Route::post('/client/document-signing/{id}/sign', [App\Http\Controllers\DocumentSigningController::class, 'sign'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client');

Route::post('/client/document-signing/{id}/sync', [App\Http\Controllers\DocumentSigningController::class, 'sync'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client');

Route::get('/client/document-signing/{id}/download/{fileIndex?}', [App\Http\Controllers\DocumentSigningController::class, 'download'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client');

Route::get('/client/worker-profile/{workerId}', [App\Http\Controllers\ClientController::class, 'viewWorkerProfile'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client');

Route::post('/client/endorsements/schedule', [App\Http\Controllers\ClientController::class, 'scheduleEndorsementMeetAndGreet'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client');

Route::put('/client/endorsements/reschedule', [App\Http\Controllers\ClientController::class, 'rescheduleEndorsementMeetAndGreet'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client');

Route::get('/client/job-postings/create', [App\Http\Controllers\JobPostingController::class, 'create'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client');

Route::post('/client/job-postings', [App\Http\Controllers\JobPostingController::class, 'store'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client');

Route::get('/client/job-postings/{id}/edit', [App\Http\Controllers\JobPostingController::class, 'edit'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client');

Route::put('/client/job-postings/{id}', [App\Http\Controllers\JobPostingController::class, 'update'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client');

Route::delete('/client/job-postings/{id}', [App\Http\Controllers\JobPostingController::class, 'destroy'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client');

// client interview routes
Route::get('/client/interviews', [App\Http\Controllers\ClientController::class, 'interviews'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

// New interview entry creation (no existing application ID required)
Route::post('/client/interviews', [App\Http\Controllers\ClientController::class, 'createInterview'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::post('/client/interviews/schedule', [App\Http\Controllers\ClientController::class, 'scheduleInterview'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::put('/client/interviews/reschedule', [App\Http\Controllers\ClientController::class, 'rescheduleInterview'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::post('/client/interviews/addNotes', [App\Http\Controllers\ClientController::class, 'addInterviewNotes'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::delete('/client/interviews/reject/{id}', [App\Http\Controllers\ClientController::class, 'rejectApplication'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::post('/client/test-email', [App\Http\Controllers\ClientController::class, 'sendTestEmail'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':client');

Route::post('/client/interviews/complete/{id}', [App\Http\Controllers\ClientController::class, 'completeInterview'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::post('/client/interviews/hire/{id}', [App\Http\Controllers\ClientController::class, 'hireApplicant'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::get('/client/interviews/{id}', [App\Http\Controllers\ClientController::class, 'interviewDetails'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

// Admin access for application and interview management
Route::get('/admin/applications', [App\Http\Controllers\ClientController::class, 'applications'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::get('/admin/applications/{applicationId}/download', [App\Http\Controllers\ClientController::class, 'downloadAttachment'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::get('/admin/interviews', [App\Http\Controllers\ClientController::class, 'interviews'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::post('/admin/interviews', [App\Http\Controllers\ClientController::class, 'createInterview'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::post('/admin/interviews/schedule', [App\Http\Controllers\ClientController::class, 'scheduleInterview'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::put('/admin/interviews/reschedule', [App\Http\Controllers\ClientController::class, 'rescheduleInterview'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::post('/admin/interviews/addNotes', [App\Http\Controllers\ClientController::class, 'addInterviewNotes'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::delete('/admin/interviews/reject/{id}', [App\Http\Controllers\ClientController::class, 'rejectApplication'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::post('/admin/interviews/complete/{id}', [App\Http\Controllers\ClientController::class, 'completeInterview'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::post('/admin/interviews/hire/{id}', [App\Http\Controllers\ClientController::class, 'hireApplicant'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::get('/admin/interviews/{id}', [App\Http\Controllers\ClientController::class, 'interviewDetails'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::get('/admin/finalization', [App\Http\Controllers\ClientController::class, 'finalization'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::get('/admin/document-signing', [App\Http\Controllers\DocumentSigningController::class, 'index'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::post('/admin/document-signing', [App\Http\Controllers\DocumentSigningController::class, 'store'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::post('/admin/document-signing/{id}/send', [App\Http\Controllers\DocumentSigningController::class, 'send'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::post('/admin/document-signing/{id}/sync', [App\Http\Controllers\DocumentSigningController::class, 'sync'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::post('/admin/document-signing/{id}/void', [App\Http\Controllers\DocumentSigningController::class, 'void'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::get('/admin/document-signing/{id}/download/{fileIndex?}', [App\Http\Controllers\DocumentSigningController::class, 'download'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::get('/admin/endorsements/create', [App\Http\Controllers\ClientController::class, 'createEndorsement'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::post('/admin/endorsements', [App\Http\Controllers\ClientController::class, 'storeEndorsement'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::get('/admin/settings', [App\Http\Controllers\AdminController::class, 'settings'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::get('/admin/cms', [App\Http\Controllers\CmsController::class, 'index'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::post('/admin/cms/brand', [App\Http\Controllers\CmsController::class, 'updateBrand'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::post('/admin/cms/palette', [App\Http\Controllers\CmsController::class, 'updatePalette'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::post('/admin/cms/pages/{slug}', [App\Http\Controllers\CmsController::class, 'updatePage'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::post('/admin/cms/services/{slug}', [App\Http\Controllers\CmsController::class, 'updateService'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::post('/admin/cms/reset', [App\Http\Controllers\CmsController::class, 'reset'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::post('/admin/settings/ndis-requirements', [App\Http\Controllers\AdminController::class, 'storeRequirement'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::delete('/admin/settings/ndis-requirements/{id}', [App\Http\Controllers\AdminController::class, 'destroyRequirement'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

// healthcare registration handled by controller so that it creates both user and profile
Route::get('/healthcare-register', [App\Http\Controllers\HealthcareController::class, 'showRegistrationForm']);
Route::post('/healthcare-register/parse-resume', [App\Http\Controllers\HealthcareController::class, 'parseResume']);
Route::post('/healthcare-register', [App\Http\Controllers\HealthcareController::class, 'register']);
Route::get('/healthcare-jobs', [App\Http\Controllers\HealthcareController::class, 'jobs'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':healthcare_worker');

Route::get('/healthcare-jobs-details/{id}', [App\Http\Controllers\HealthcareController::class, 'jobDetails'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':healthcare_worker');

Route::post('/job-postings/{id}/apply', [App\Http\Controllers\HealthcareController::class, 'apply'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':healthcare_worker');

Route::get('/applications', [App\Http\Controllers\HealthcareController::class, 'applications'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':healthcare_worker');

Route::get('/applications/{applicationId}/download', [App\Http\Controllers\HealthcareController::class, 'downloadAttachment'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':healthcare_worker');

Route::put('/applications/{applicationId}', [App\Http\Controllers\HealthcareController::class, 'updateApplication'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':healthcare_worker');

Route::delete('/applications/{applicationId}', [App\Http\Controllers\HealthcareController::class, 'destroyApplication'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':healthcare_worker');

// profile page for healthcare workers
Route::get('/healthcare-profile', [App\Http\Controllers\HealthcareController::class, 'profile'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':healthcare_worker');
Route::post('/healthcare-profile', [App\Http\Controllers\HealthcareController::class, 'update'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':healthcare_worker');
Route::post('/healthcare-profile/photo', [App\Http\Controllers\HealthcareController::class, 'updateProfilePhoto'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':healthcare_worker');
Route::post('/healthcare-profile/skills', [App\Http\Controllers\HealthcareController::class, 'updateSkills'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':healthcare_worker');
Route::get('/admin-registrations', [App\Http\Controllers\AdminController::class, 'registrations'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');
Route::get('/admin/clients/create', [App\Http\Controllers\AdminClientRegistrationController::class, 'create'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin')
    ->name('admin.clients.create');
Route::post('/admin/clients', [App\Http\Controllers\AdminClientRegistrationController::class, 'store'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin')
    ->name('admin.clients.store');
Route::post('/admin-registrations/{id}/approve', [App\Http\Controllers\AdminController::class, 'approve'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');
Route::post('/admin-registrations/{id}/reject', [App\Http\Controllers\AdminController::class, 'reject'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');
Route::get('/admin-registrations/{id}/resume', [App\Http\Controllers\AdminController::class, 'downloadResume'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::post('/admin-registrations/{id}/impersonate-client', [App\Http\Controllers\AdminController::class, 'impersonateClient'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');

Route::post('/admin/impersonation/stop', [App\Http\Controllers\AdminController::class, 'stopImpersonating']);

Route::get('/document-signing/{documentSigningRequest}/return', [App\Http\Controllers\DocumentSigningController::class, 'returnFromSigning'])
    ->name('document-signing.return');

Route::post('/docusign/connect', [App\Http\Controllers\DocumentSigningController::class, 'webhook'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);
    
// Finalization page for client
Route::get('/client/finalization', [App\Http\Controllers\ClientController::class, 'finalization'])
    ->middleware(\App\Http\Middleware\CheckAccountType::class . ':admin');
