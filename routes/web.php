<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ManageFeedbacks;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Feedbacks\CreateFeedback;
use App\Livewire\Feedbacks\FeedbackDetail;
use App\Livewire\Feedbacks\FeedbackList;
use App\Livewire\Surveys\FillSurvey;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 1. Halaman Publik / Beranda Aspirasi
Route::get('/', FeedbackList::class)->name('feedbacks.index');
Route::get('/feedbacks', FeedbackList::class);

// 2. Kuesioner & Form Survei Kepuasan Mahasiswa
Route::get('/surveys/{survey}', FillSurvey::class)->name('surveys.fill');

// 3. Buat Aspirasi (Khusus User yang Sudah Login)
Route::get('/feedbacks/create', CreateFeedback::class)
    ->middleware('auth')
    ->name('feedbacks.create');

// 3. Detail Aspirasi, Tracking Status, & Diskusi
Route::get('/feedbacks/{feedback}', FeedbackDetail::class)
    ->name('feedbacks.detail');

// 4. Autentikasi (Tamu / Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

// 5. Logout
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    session()->flash('success', 'Anda telah berhasil keluar.');
    return redirect()->route('feedbacks.index');
})->middleware('auth')->name('logout');

// 6. Area Khusus Administrator (Role: Admin)
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('admin.dashboard');
    Route::get('/feedbacks', ManageFeedbacks::class)->name('admin.feedbacks');
});
