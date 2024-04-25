<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

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

Route::get('/', function () {

//    dd(auth()->user());


    return Inertia::render('Test/Landing');
})->name('default');

/* >>>>>Roles routes <<<<<< */

//Get all roles
Route::get('/roles', [\App\Http\Controllers\Roles\RoleController::class, 'index'])->middleware(['auth', 'isAdmin'])->name('roles.index');
//Roles api
Route::resource('api/roles', \App\Http\Controllers\Roles\ApiRoleController::class, [
    'as' => 'api'
])->middleware('auth');


/* >>>>>>>>>>>>>>>>>>>>>>>>>>>> AcademicPrograms routes <<<<<<<<<<<<<<<<<<<<<<<<<<< */
Route::inertia('/academicPrograms', 'AcademicPrograms/Index')->middleware(['auth', 'isAdmin'])->name('academicPrograms.index');
Route::resource('api/academicPrograms', \App\Http\Controllers\AcademicProgramController::class, [
    'as' => 'api'
]);
Route::post('/api/academicPrograms/sync', [\App\Http\Controllers\AcademicProgramController::class, 'sync'])->middleware(['auth', 'isAdmin'])
    ->name('api.academicPrograms.sync');

/* >>>>>>>>>>>>>>>>>>>>>>>>>>>> Forms routes <<<<<<<<<<<<<<<<<<<<<<<<<<< */
Route::inertia('/forms', 'Forms/Index')->middleware(['auth', 'isAdmin'])->name('forms.index.view');

Route::inertia('/forms/{form}', 'Forms/Show')->middleware(['auth', 'isAdmin'])->name('forms.show.view');

Route::resource('api/forms', \App\Http\Controllers\FormController::class, [
    'as' => 'api'
])->middleware('auth');
Route::get('api/forms/{form}/formQuestions', [\App\Http\Controllers\FormQuestionController::class, 'getByFormId'])->name('api.forms.questions.show')
    ->middleware(['auth']);
Route::patch('api/forms/{form}/formQuestions', [\App\Http\Controllers\FormQuestionController::class, 'storeOrUpdate'])->name('api.forms.questions.store')
    ->middleware(['auth']);



/* >>>>>>>>>>>>>>>>>>>>>>>>>>>> Results routes <<<<<<<<<<<<<<<<<<<<<<<<<<< */
Route::inertia('/results/report', 'Results/Report')->middleware(['auth', 'isAdmin'])->name('results.report');
Route::post('/results/graph', [\App\Http\Controllers\FormAnswerResultController::class, 'showGraph'])->name('results.showGraph');
Route::post('results/academicPrograms',[ \App\Http\Controllers\FormAnswerResultController::class, 'index'])->name('results.academicPrograms');
Route::get('results/specificReport',[ \App\Http\Controllers\FormAnswerResultController::class, 'downloadSpecificReport'])->name('results.specificReport');


/* >>>>>>>>>>>>>>>>>>>>>>>>>>>> Test routes <<<<<<<<<<<<<<<<<<<<<<<<<<< */
Route::get('/tests', [\App\Http\Controllers\TestController::class, 'indexView'])->middleware(['auth'])->name('tests.index.view');
Route::post('/tests/validateInfo', [\App\Http\Controllers\TestController::class, 'validateUserInputData'])->name('tests.validateInfo');
Route::post('/tests/start', [\App\Http\Controllers\TestController::class, 'startTest'])->name('tests.startTest');
Route::resource('api/tests', \App\Http\Controllers\TestController::class, [
    'as' => 'api']);




/* >>>>>User routes <<<<<< */

//Get all users
Route::get('/users', [\App\Http\Controllers\Users\UserController::class, 'index'])->middleware(['auth', 'isAdmin'])->name('users.index');
//users api
Route::resource('api/users', \App\Http\Controllers\Users\ApiUserController::class, [
    'as' => 'api'
])->middleware('auth');
//Update user role
Route::patch('/api/users/{user}/roles', [\App\Http\Controllers\Users\ApiUserController::class, 'updateUserRole'])->middleware('auth')->name('api.users.roles.update');
Route::get('/api/users/{user}/roles', [\App\Http\Controllers\Users\ApiUserController::class, 'getUserRole'])->middleware('auth')->name('api.users.roles.show');




/* >>>>>Roles routes <<<<<< */
Route::get('landing', function () {
    return Inertia::render('Test/Landing');
})->name('landing');





//Auth routes
Route::get('login', [\App\Http\Controllers\AuthController::class, 'redirectGoogleLogin'])->name('login');
Route::get('/google/callback', [\App\Http\Controllers\AuthController::class, 'handleGoogleCallback']);

