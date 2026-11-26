<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Folders\BasicInfoController;
use App\Http\Controllers\Folders\BeneficiaryController;
use App\Http\Controllers\Folders\BeneficiaryExportController;
use App\Http\Controllers\Folders\LegalFolderController;
use App\Http\Controllers\Folders\MedicalFolderController;
use App\Http\Controllers\Folders\SocialFolderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MainPanel\NewLocationController;
use App\Http\Controllers\MainPanel\ReportsController;
use App\Http\Controllers\MainPanel\SearchController;
use App\Http\Controllers\MainPanel\UsersController;
use App\Http\Controllers\MedicalUploadController;
use App\Http\Controllers\UploadFileController;
use App\Http\Middleware\SetLocale;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
 * Access:
 * - "auth" and "activated": every page of the main panel needs a logged-in user
 *   with an active account (activated, and not deactivated).
 * - "role:…": the folder of each profession can be changed only by that
 *   profession and by administrators. Everyone with access can read every folder.
 * - "admin": users, places of examination, CSV import and export, deletion of files.
 * Every request that changes data is a POST with a CSRF token.
 */

$doctor = 'role:'.User::ROLE_DOCTOR;
$lawyer = 'role:'.User::ROLE_LAWYER;
$psychologist = 'role:'.User::ROLE_PSYCHOLOGIST;
// The psychosocial folder: social workers keep the social history, psychologists the sessions.
$psychosocial = 'role:'.User::ROLE_SOCIAL_WORKER.','.User::ROLE_PSYCHOLOGIST;

Route::redirect('/', '/auth/login');

// The language of the interface: Greek or English, for guests and users.
Route::get('language/{locale}', function (string $locale) {
    abort_unless(array_key_exists($locale, SetLocale::LANGUAGES), 404);
    session(['locale' => $locale]);

    return redirect()->back(fallback: '/');
})->name('language');

// Login, registration and password reset.
Route::middleware('guest')->group(function () {
    Route::get('auth/login', [LoginController::class, 'show'])->name('login');
    Route::post('auth/login', [LoginController::class, 'login']);
    Route::get('auth/register', [RegisterController::class, 'show']);
    Route::post('auth/register', [RegisterController::class, 'register']);
    Route::get('password/email', [PasswordController::class, 'showLinkRequest']);
    Route::post('password/email', [PasswordController::class, 'sendLink'])->middleware('throttle:5,1');
    Route::get('password/reset/{token}', [PasswordController::class, 'showReset'])->name('password.reset');
    Route::post('password/reset', [PasswordController::class, 'reset']);
});

Route::middleware('auth')->group(function () {
    Route::post('auth/logout', [LoginController::class, 'logout']);
    Route::view('awaitingActivation', 'auth.awaitActivation');
    Route::view('disabledUser', 'auth.disabledUser');
});

Route::middleware(['auth', 'activated'])->group(function () use ($doctor, $lawyer, $psychologist, $psychosocial) {
    Route::get('home', [BeneficiaryController::class, 'getBenefitersList']);
    Route::get('main-panel', [HomeController::class, 'index']);

    // Beneficiaries.
    Route::get('benefiters-list', [BeneficiaryController::class, 'getBenefitersList']);

    // Basic information: every profession can change it.
    Route::get('benefiter/{id}/basic-info', [BasicInfoController::class, 'getBasicInfo']);
    Route::post('benefiter/{id}/basic-info', [BasicInfoController::class, 'postBasicInfo']);
    Route::post('benefiter/{id}/basic-info/referrals', [BasicInfoController::class, 'postBasicInfoReferrals']);
    Route::post('benefiter/{id}/basic-info/referral-delete/{referral_id}', [BasicInfoController::class, 'deleteBasicInfoReferral']);
    Route::post('benefiter/{id}/new-occurrence-save', [BasicInfoController::class, 'saveOccurrencesBasicInfo']);
    Route::post('benefiter/{id}/edit-occurrence/{occurrence_id}', [BasicInfoController::class, 'editOccurrencesBasicInfo']);
    Route::post('benefiter/{id}/delete-occurrence/{occurrence_id}', [BasicInfoController::class, 'deleteOccurrencesBasicInfo']);
    Route::post('check-for-possible-duplicates', [BasicInfoController::class, 'postCheckForDuplicatedFolders']);

    // Medical folder: doctors change it.
    Route::get('benefiter/{id}/medical-folder', [MedicalFolderController::class, 'getMedicalFolder']);
    Route::post('benefiter/{id}/medical-folder', [MedicalFolderController::class, 'postMedicalFolder'])->middleware($doctor);
    Route::get('benefiter/getIC10List', [MedicalFolderController::class, 'getICD10List']);
    Route::get('benefiter/getMedicationList', [MedicalFolderController::class, 'getMedicationList']);
    Route::get('benefiter/{id}/getEachMedicalVisit', [MedicalFolderController::class, 'getMedicalVisitModal']);
    Route::get('benefiter/{id}/editMedicalVisit', [MedicalFolderController::class, 'getMedicalVisitForEditing'])->middleware($doctor);
    Route::post('benefiter/{id}/editMedicalVisit', [MedicalFolderController::class, 'postMedicalVisitFromEditing'])->middleware($doctor);
    Route::get('medical-uploads/{upload}', [MedicalUploadController::class, 'show']);

    // Legal folder: lawyers change it.
    Route::get('benefiter/{id}/legal-folder', [LegalFolderController::class, 'getLegalFolder']);
    Route::post('benefiter/{id}/legal-folder', [LegalFolderController::class, 'postLegalFolder'])->middleware($lawyer);
    Route::post('benefiter/{id}/legal-session', [LegalFolderController::class, 'postLegalSession'])->middleware($lawyer);

    // Psychosocial folder.
    Route::get('benefiter/{id}/social-folder', [SocialFolderController::class, 'getSocialFolder']);
    Route::post('benefiter/{id}/social-folder', [SocialFolderController::class, 'postSocialFolder'])->middleware($psychosocial);
    Route::post('benefiter/{id}/session-save', [SocialFolderController::class, 'postSessionSave'])->middleware($psychologist);
    Route::post('benefiter/{id}/session-edit/{session_id}', [SocialFolderController::class, 'postSessionEdit'])->middleware($psychologist);
    Route::post('benefiter/{id}/session-delete/{session_id}', [SocialFolderController::class, 'getSessionDelete'])->middleware($psychologist);

    // Search and reports.
    Route::get('search', [SearchController::class, 'getSearch']);
    Route::get('results', [SearchController::class, 'getResults']);
    Route::get('reports', [ReportsController::class, 'getReports']);
    Route::get('reports-search-results', [ReportsController::class, 'getBenefiterSearchResults']);
    Route::get('benefites-VS-education-Report-get-data', [ReportsController::class, 'getBenefitesVSeducationdata']);
    Route::get('benefites-VS-doctor-Report-get-data', [ReportsController::class, 'getBenefitesVSdoctorsData']);
    Route::get('benefites-VS-ClinicalConditions-Report-get-data', [ReportsController::class, 'getBenefitesVSClinicalConditionsData']);
    Route::get('medical_visits-PER-month-Report-get-data', [ReportsController::class, 'getMedicalVisitsVSMonthDate']);
    Route::get('benefites-VS-phycological-support-type', [ReportsController::class, 'getBenefitersVSPhycologicalSupportType']);
    Route::get('registrations-PER-month-Report-get-data', [ReportsController::class, 'getRegistrationsVSMonthDate']);

    // Administration.
    Route::middleware('admin')->group(function () {
        Route::post('benefiter/{id}/delete', [BeneficiaryController::class, 'getDeleteBenefiter']);
        Route::get('new-benefiter/uploadCSV', [UploadFileController::class, 'getUploadCSV']);
        Route::post('new-benefiter/uploadCSV', [UploadFileController::class, 'excelUpload']);
        Route::get('download-history-csv', [BeneficiaryExportController::class, 'folderHistory']);
        Route::get('download-benefiters-csv', [BeneficiaryExportController::class, 'beneficiaries']);
        Route::get('new-location', [NewLocationController::class, 'getNewLocationView']);
        Route::post('save-location', [NewLocationController::class, 'postSaveNewLocation']);
        Route::get('main-panel/users-list', [UsersController::class, 'UsersList']);
        Route::post('main-panel/users-list', [UsersController::class, 'UserStatusUpdate']);
    });
});
