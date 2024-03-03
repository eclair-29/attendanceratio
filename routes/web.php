<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailerController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\UtilitiesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::controller(MailerController::class)->group(function () {
    Route::get('/notifications/approval', 'getApprovalNotification')->name('notifications.approval');
    Route::get('/notifications/feedback', 'getRejectionFeedback')->name('notifications.feedback');
    Route::get('/notifications/download', 'downloadRatioByDivision')->name('notifications.download');
    Route::get('/notifications/send', 'sendMailNotification')->name('notifications.send');
    Route::put('/notifications/rejection/{id}', 'postRejectionReason')->name('notifications.rejection');
});

Route::controller(AttendanceController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/export/division', 'exportByDivision')->name('export.division');
    Route::get('/export/series', 'exportBySeries')->name('export.series');
    Route::get('/download', 'downloadUploadedFile')->name('download');
    Route::get('/clear', 'clearBatchingTables')->name('clear');
    Route::get('/progress', 'getFileUploadProgress')->name('progress');
    Route::get('/series', 'getSeries')->name('series');
    Route::get('/ratio/series', 'getRatioBySeries')->name('ratio.series');
    Route::post('/upload/base', 'uploadBase')->name('upload.base');
    Route::post('/upload/attendance', 'uploadAttendance')->name('upload.attendance');
});

Route::controller(UtilitiesController::class)->group(function () {
    Route::get('/utilities/bu', 'editBuMalingAddress')->name('utilities.bu');
    Route::put('/utilities/bu/{id}', 'updateBuMailingAddress')->name('utilities.bu.update');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
