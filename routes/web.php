<?php
use App\Http\Controllers\{AssignmentController, AttendanceController, WorkingDayController, DashboardController, LookupController, StudentController, SupervisorController, UserController};
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
});
Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/', DashboardController::class)->middleware('can:dashboard.view')->name('dashboard');

    Route::resource('users', UserController::class)->except(['show', 'destroy']);
    Route::patch('users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
    Route::get('users/{user}/permissions', [UserController::class, 'editPermissions'])->name('users.permissions.edit');
    Route::put('users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('users.permissions.update');

    Route::resource('students', StudentController::class);
    Route::resource('supervisors', SupervisorController::class)->except(['destroy']);
    Route::patch('supervisors/{supervisor}/toggle', [SupervisorController::class, 'toggle'])->name('supervisors.toggle');
    Route::get('assignments', [AssignmentController::class, 'index'])->name('assignments.index');
    Route::post('assignments/transfer', [AssignmentController::class, 'transfer'])->name('assignments.transfer');

    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
    Route::get('attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
    Route::get('settings/working-days', [WorkingDayController::class, 'edit'])->middleware('can:settings.view')->name('working-days.edit');
    Route::put('settings/working-days', [WorkingDayController::class, 'update'])->middleware('can:settings.update')->name('working-days.update');

    Route::prefix('settings/lookups')->name('lookups.')->group(function () {
        Route::get('/', [LookupController::class, 'index'])->middleware('can:settings.view')->name('index');
        Route::middleware('can:settings.update')->group(function () {
            Route::post('{type}', [LookupController::class, 'store'])->name('store');
            Route::put('{type}/{id}', [LookupController::class, 'update'])->name('update');
            Route::patch('{type}/{id}/toggle', [LookupController::class, 'toggle'])->name('toggle');
        });
    });
});
