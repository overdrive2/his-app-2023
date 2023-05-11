<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth:sanctum'])->prefix('nurse')->group(function () {
    Route::get('/', [App\Http\Livewire\NurseModule\NurseIpdList::class, '__invoke'])->name('nurse.ipdlist'); // หน้าแรก
    Route::get('newcase', [App\Http\Livewire\NurseModule\IpdNewCases::class, '__invoke'])->name('nurse.newcase'); // หน้ารับใหม่
    Route::get('newcase/entry', [App\Http\Livewire\NurseModule\NewcaseEntry::class, '__invoke'])->name('newcase.entry'); // หน้ารับใหม่
});
