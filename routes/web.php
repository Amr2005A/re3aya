<?php
use App\Http\Controllers\{CircleController, CriterionController, PointController, PointSettingsController, NoteController, AssignmentController, AttendanceController, WorkingDayController, DashboardController, LookupController, StudentController, SupervisorController, UserController};
use App\Http\Controllers\Auth\{LoginController, RegisterController};
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:5,10');
});
Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/', DashboardController::class)->name('dashboard');

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

    Route::get('notes', [NoteController::class, 'index'])->name('notes.index');
    Route::post('students/{student}/notes', [NoteController::class, 'store'])->name('students.notes.store');
    Route::put('notes/{note}', [NoteController::class, 'update'])->name('notes.update');
    Route::delete('notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

    Route::resource('circles', CircleController::class)->except(['show']);
    Route::post('circles/{circle}/criteria', [CriterionController::class, 'store'])->name('circles.criteria.store');
    Route::put('criteria/{criterion}', [CriterionController::class, 'update'])->name('criteria.update');
    Route::patch('criteria/{criterion}/toggle', [CriterionController::class, 'toggle'])->name('criteria.toggle');
    Route::delete('criteria/{criterion}', [CriterionController::class, 'destroy'])->name('criteria.destroy');
    Route::get('points', [PointController::class, 'index'])->name('points.index');
    Route::get('points/create', [PointController::class, 'create'])->name('points.create');
    Route::post('points', [PointController::class, 'store'])->name('points.store');
    Route::post('points/{transaction}/reverse', [PointController::class, 'reverse'])->name('points.reverse');
    Route::get('settings/points', [PointSettingsController::class, 'edit'])->middleware('can:settings.view')->name('points-settings.edit');
    Route::put('settings/points', [PointSettingsController::class, 'update'])->middleware('can:settings.update')->name('points-settings.update');

    Route::prefix('settings/lookups')->name('lookups.')->group(function () {
        Route::get('/', [LookupController::class, 'index'])->middleware('can:settings.view')->name('index');
        Route::middleware('can:settings.update')->group(function () {
            Route::post('{type}', [LookupController::class, 'store'])->name('store');
            Route::put('{type}/{id}', [LookupController::class, 'update'])->name('update');
            Route::patch('{type}/{id}/toggle', [LookupController::class, 'toggle'])->name('toggle');
        });
    });
});
