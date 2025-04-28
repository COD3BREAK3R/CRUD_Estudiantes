<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\EstudianteController;

Route::get('/', function () {
    
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Rutas de autenticación
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'create']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return redirect()->route('estudiantes.index');
    })->name('dashboard');

    // Rutas de Estudiantes
    Route::resource('estudiantes', EstudianteController::class);
    
    // Rutas de Exportación
    Route::get('/estudiantes-export-pdf', [EstudianteController::class, 'exportPDF'])->name('estudiantes.export.pdf');
    Route::get('/estudiantes-export-excel', [EstudianteController::class, 'exportExcel'])->name('estudiantes.export.excel');
    
    // Seeder de exportación
    Route::get('/run-estudiantes-seeder', function () {
        if (app()->environment('local')) {
            \Artisan::call('db:seed', ['--class' => 'EstudianteSeeder']);
            return redirect()->route('estudiantes.index')
                ->with('success', '¡20 estudiantes han sido generados exitosamente!');
        }
        
        return abort(404);
    })->name('run.estudiantes.seeder');
});