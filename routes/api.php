<?php

use App\Entities\RdtApplicant;
use App\Entities\RdtEvent;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the 'api' middleware group. Enjoy building your API!
|
*/

$proxyUrl    = config('proxy.url');
$proxyScheme = config('proxy.scheme');

if (!empty($proxyUrl)) {
    URL::forceRootUrl($proxyUrl);
}

if (!empty($proxyScheme)) {
    URL::forceScheme($proxyScheme);
}

Route::get('/', 'HomeController');
// RDT Registration
Route::post('rdt/register', 'Rdt\RdtRegisterController');
Route::get('rdt/check-event', 'Rdt\RdtCheckEventController');
Route::post('rdt/check', 'Rdt\RdtCheckStatusController');
Route::get('rdt/register/download', 'Rdt\RdtRegisterDownloadController')->name('registration.download');
Route::get('rdt/qrcode', 'Rdt\RdtQrCodeController')->name('registration.qrcode');

Route::post('rdt/survey', 'Rdt\RdtSurveyStoreController');

// Checkin App
Route::post('rdt/checkin', 'Rdt\RdtCheckinController');
Route::post('rdt/event-check', 'Rdt\RdtEventCheckController');
Route::post('checkin/applicant-profile', 'Checkin\ApplicantCheckProfileController');
Route::post('checkin/event/participants', 'Checkin\RdtEventParticipantsController');
Route::post('register/check-nik', 'Register\CheckNikIsAlreadyUsedController');

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/user', 'Settings\ProfileController@index');
});

// RDT Events
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('rdt/events', 'Rdt\RdtEventController@index')->middleware('can:viewAny,' . RdtEvent::class);
    Route::get('rdt/events/{rdtEvent}', 'Rdt\RdtEventController@show')->middleware('can:view,rdtEvent');
    Route::post('rdt/events', 'Rdt\RdtEventController@store')->middleware('can:create,' . RdtEvent::class);
    Route::put('rdt/events/{rdtEvent}', 'Rdt\RdtEventController@update')->middleware('can:update,rdtEvent');
    Route::delete('rdt/events/{rdtEvent}', 'Rdt\RdtEventController@destroy')->middleware('can:delete,rdtEvent');
});

// RDT Event Invitations Participants
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('rdt/events/{rdtEvent}/participants', 'Rdt\RdtEventParticipantListController')
        ->middleware('can:view,rdtEvent');

    Route::post('rdt/events/{rdtEvent}/participants', 'Rdt\RdtEventParticipantAddController');
    Route::post('rdt/events/{rdtEvent}/participants-remove', 'Rdt\RdtEventParticipantRemoveController');
    Route::post('rdt/events/{rdtEvent}/participants-notify', 'Rdt\RdtEventNotifyParticipantController');
    Route::post('rdt/events/{rdtEvent}/participants-notify-result', 'Rdt\RdtEventNotifyTestResultController');

    Route::post('rdt/events/{rdtEvent}/participants-import', 'Rdt\RdtEventParticipantImportController');
    Route::get('rdt/events/{rdtEvent}/participants-export', 'Rdt\RdtEventParticipantListExportController')
        ->middleware('can:view,rdtEvent');
    Route::get('rdt/events/{rdtEvent}/participants-export-f1', 'Rdt\RdtInvitationExportExcelF1Controller');

    Route::post('rdt/events/{rdtEvent}/participants-import-results', 'Rdt\RdtEventParticipantImportResultController');

    Route::put('rdt/events/{rdtEvent}/participants-set-labcode', 'Rdt\RdtEventParticipantSetLabCodeController');
});

// RDT Applicants
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('rdt/applicants', 'Rdt\RdtApplicantController@index')->middleware('can:viewAny,' . RdtApplicant::class);
    Route::get('rdt/applicants/{rdtApplicant}', 'Rdt\RdtApplicantController@show')->middleware('can:view,rdtApplicant');
    Route::post('rdt/applicants', 'Rdt\RdtApplicantController@store')->middleware('can:create,' . RdtApplicant::class);
    Route::put('rdt/applicants/{rdtApplicant}', 'Rdt\RdtApplicantController@update')
        ->middleware('can:update,rdtApplicant');
    Route::delete('rdt/applicants/{rdtApplicant}', 'Rdt\RdtApplicantController@destroy')
        ->middleware('can:delete,rdtApplicant');
});

Route::group(['middleware' => 'guest:api'], function () {
    // API for Master data
    Route::prefix('master')->namespace('Master')->group(function () {
        Route::get('areas', 'AreaController@index');
        Route::get('areas/{area}', 'AreaController@show');
    });
});
