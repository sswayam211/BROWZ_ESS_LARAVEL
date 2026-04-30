<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\Routing\Route as RoutingRoute;
use App\Http\Controllers\AppraisalController;


// index page route
Route::get('/', function () {
    return view('index', [
        'loginId' => request()->cookie('LOGIN_ID'),
        'loginPassword' => request()->cookie('LOGIN_PASSWORD'),
        'rememberMe' => request()->cookie('REMEMBER_ME'),
    ]);
});


// show all login data
Route::resource('showLoginData', AppraisalController::class);


// logout route
Route::get('/logout',[AppraisalController::class,'logout'])->name('logout');


// route to handle post request when login
Route::post('/login', [AppraisalController::class, 'verifyLogin'])->name('login.verify');


// route to open dashboard after successfull login
Route::get('/dashboard', [AppraisalController::class, 'dashboard'])->name('dashboard');


// apprasial route
Route::get('/appraisal', [AppraisalController::class, 'showAppraisal'])->name('appraisal');


// route to display appraisal emp data in modal
Route::get('/appraisal_form', [AppraisalController::class, 'ShowAppraisalForm'])->name('appraisal.form');


// route to save appraisal data 
Route::post('/save_appraisal_data_by_sup', [AppraisalController::class, 'SaveAppraisalDataBySup'])->name('saveAppraisal');


// creating route to approve apraisal
Route::get('/approve_appraisal', [AppraisalController::class, 'ShowAppraisalSuggestions'])->name('ApproveAppraisal');


// route to open appraisal aproval modal
Route::get('/approve_appraisal_form', [AppraisalController::class, 'ShowApproveAppraisalForm'])->name('ApproveAppraisalForm');


// route to save appraisal by am and hr
Route::post('/save_appraisal_data_by_amhr', [AppraisalController::class, 'SaveAppraisalDataByAMHR'])->name('saveAppraisalByAMHR');


// download appraisal final print 
Route::post('/download-appraisal', [AppraisalController::class, 'DownloadAppraisalFinalPrint'])->name('appraisal.download');


// route to open kpi master
Route::get('/kpi-master',[AppraisalController::class,'ShowKPIData'])->name('appraisal.kpi-master');


// route to display kpi add/update form
Route::get('/kpi-add-update-form',[AppraisalController::class,'ShowKpiForm'])->name('appraisal.kpi-form');


// route to add and update kpi data
Route::post('/handle-kpi-data',[AppraisalController::class,'AddUpdateKpiData'])->name('appraisal.kpi-add-update');