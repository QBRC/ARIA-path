<?php

use App\Mail\NoticeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\UsersController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\BatchesController;
use App\Http\Controllers\AnnotationController;
use App\Http\Controllers\ApiHealthCheckController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ModeController;
use App\Http\Controllers\ColorPaletteController;
use Spatie\Activitylog\Models\Activity;

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

Auth::routes();

Route::get('/', [HomeController::class,'index']);

Route::group(['middleware' => ['auth']], function () {
    Route::get('/thank-you',function (){
        return view('auth/register_success');
    });

    Route::get('/request-activation', [App\Http\Controllers\EmailController::class,'activateNewUser']);
});



Route::group(['middleware' => ['auth','active']], function () {

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */


    Route::get('/profile', [UsersController::class,'profile']);

    Route::get('/changepassword', [UsersController::class,'changPasswordForm']);
    Route::post('/changepassword', [UsersController::class,'changPassword'])->name('changepassword');

    /*
    |--------------------------------------------------------------------------
    | Data records
    |--------------------------------------------------------------------------
    */
    Route::get('/slides', [ImagesController::class,'index']);
    Route::post('/fetch-table', [ImagesController::class,'fetchTable'])->name('fetchDT');
    Route::post('/fetch-batch', [ImagesController::class,'fetchBatch'])->name('fetchBatch');
    Route::get('/table', [ImagesController::class,'imgTable']);
    Route::get('/history/{image}', [ImagesController::class,'historyTable']);
    Route::get('/slides/{image}/{annotation}', [ImagesController::class,'show']);
    Route::post('/fetch-annotator', [ImagesController::class,'fetchAnnotator'])->name('fetch-annotator');
    Route::post('/who-can-annotate', [ImagesController::class,'batchAnnotator'])->name('who-can-annotate');

    Route::get('/download/{image}/{annotations}', [ImagesController::class,'download']);
    /*
    |--------------------------------------------------------------------------
    | Image and annotation
    |--------------------------------------------------------------------------
    */

    Route::post('get-note', [AnnotationController::class,'fetch']);
    Route::post('save-note', [AnnotationController::class,'save']);
    Route::post('history', [AnnotationController::class,'loadHistory']);
    Route::get('/download/{projectName}', function ($projectName) {
        $filePath = public_path("images/csv/{$projectName}-uuid.csv");
        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }
        return response()->download($filePath);
    })->name('downloadSlidesCSV');

    /*
    |--------------------------------------------------------------------------
    | ColorPalette
    |--------------------------------------------------------------------------
    */
    Route::post('/save-color-palette', [ColorPaletteController::class, 'savePalette']);
    Route::get('/get-color-palette/{imageId}/{userId}', [ColorPaletteController::class, 'getPalette']);


    // Edit
//    Route::resource('datarecords', DataRecordController::class)->only([
//        'edit', 'update'
//    ])->middleware('permission:1');
//
//    // Create
//    Route::resource('datarecords', DataRecordController::class)->only([
//        'store'
//    ])->middleware('permission:2');
//
//    // Delete & Restore
//    Route::group(['middleware' => ['permission:3']], function () {
//        Route::resource('datarecords', DataRecordController::class)->only(['destroy']);
//        Route::get('/datarecords/{datarecord}/delete', [DataRecordController::class,'delete']);
//        Route::get('/datarecords/{datarecord}/restore', [DataRecordController::class,'restore']);
//    });
//
//    // View
//    Route::resource('datarecords', DataRecordController::class)->only([
//        'create', 'index', 'show'
//    ]);


    /*
    |--------------------------------------------------------------------------
    | Only allow Admin to access
    |--------------------------------------------------------------------------
    */

    Route::group(['middleware' => ['admin']], function () {

        Route::resource('/users', UsersController::class);
        Route::get('/users/{user}/delete', [UsersController::class,'delete'])->where('user' , '[0-9]+');
        Route::get('/users/{user}/restore', [UsersController::class,'restore'])->where('user' , '[0-9]+');
        Route::post('/authorize', [UsersController::class,'updateAuthorization'])->name('authorize');

        Route::resource('/groups', GroupsController::class);
        Route::put('/groups/{group}/erase', [GroupsController::class,'erase']);
        Route::put('/groups/{group}/delete', [GroupsController::class,'delete']);
        Route::get('/groups/{group}/restore', [GroupsController::class,'restore']);

        Route::resource('/batches', BatchesController::class);
        Route::put('/batches/{batch}/erase', [BatchesController::class,'erase']);
        Route::put('/batches/{batch}/delete', [BatchesController::class,'delete']);
        Route::get('/batches/{batch}/restore', [BatchesController::class,'restore']);

        Route::resource('/models', ModeController::class);
        Route::put('/models/{model}/erase', [ModeController::class,'erase']);
        Route::put('/models/{model}/delete', [ModeController::class,'delete']);
        Route::get('/models/{model}/restore', [ModeController::class,'restore']);

        Route::get('/import', [ImagesController::class, 'upload'])->name('upload');
        Route::post('/upload', [ImagesController::class, 'handleUpload'])->name('upload.handle');
        Route::post('/import', [ImagesController::class, 'handleImport'])->name('upload.import');

        Route::get('/backup', [ImagesController::class, 'backupTable'])->name('backup');

        Route::get('/api-health-checks', [ApiHealthCheckController::class, 'index'])->name('api-health-checks.index');
        Route::post('/api/health-checks/{groupName}/check', [ApiHealthCheckController::class, 'checkHealth']);
        Route::post('/api-health-checks/{groupName}/save-url', [ApiHealthCheckController::class, 'saveUrl'])->name('api-health-checks.save-url');
    });



    /*
    |--------------------------------------------------------------------------
    | Admin and Auditor can access
    |--------------------------------------------------------------------------
    */
    Route::group(['middleware' => ['auditor']], function () {
        /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        */

        Route::get('/log', function (){
            return view('log',['logs'=> Activity::all()]);
        });

        /*
        |--------------------------------------------------------------------------
        | Users | Permission | Groups | Batches
        |--------------------------------------------------------------------------
        */

        Route::resource('/users', UsersController::class)->only([
            'index', 'show'
        ]);
        Route::resource('/groups', GroupsController::class)->only([
            'index', 'show'
        ]);
        Route::resource('/batches', BatchesController::class)->only([
            'index', 'show'
        ]);

    });



});
