<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormSubmissionController;

Route::get('/', function () {
    return view('welcome');
});

// Rotas públicas para formulários
Route::get('/form/{slug}', [FormSubmissionController::class, 'show'])->name('form.show');
Route::post('/form/{slug}', [FormSubmissionController::class, 'submit'])->name('form.submit');
Route::get('/form/{slug}/success', [FormSubmissionController::class, 'success'])->name('form.success');
