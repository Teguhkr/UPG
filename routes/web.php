<?php

use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DownloadController as AdminDownloadController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\InquiryController as AdminInquiryController;
use App\Http\Controllers\Admin\MediaItemController as AdminMediaItemController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Frontend\ArticleController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\DownloadController;
use App\Http\Controllers\Frontend\FaqController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\MediaController;
use App\Http\Controllers\language\LanguageController;
use Illuminate\Support\Facades\Route;

// Public Portal Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/cari', [HomeController::class, 'search'])->name('search');

// Profil
Route::get('/profil/tentang-upg', [ArticleController::class, 'tentangUpg'])->name('profil.tentang');
Route::get('/profil/hubungi-upg-kkp', [ContactController::class, 'index'])->name('contact.index');
Route::post('/profil/hubungi-upg-kkp', [ContactController::class, 'store'])->name('contact.store');

// Publikasi
Route::get('/publikasi/informasi', fn (\Illuminate\Http\Request $r) => app(ArticleController::class)->byCategory($r, 'informasi'))
    ->name('publikasi.informasi');
Route::get('/publikasi/mengenal-gratifikasi', fn (\Illuminate\Http\Request $r) => app(ArticleController::class)->byCategory($r, 'mengenal-gratifikasi'))
    ->name('publikasi.mengenal');
Route::get('/publikasi/infografis', [MediaController::class, 'infografis'])->name('publikasi.infografis');
Route::get('/publikasi/video-gratifikasi', [MediaController::class, 'videos'])->name('publikasi.videos');

// Artikel & Halaman Statis
Route::get('/artikel/{slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/halaman/{slug}', [ArticleController::class, 'page'])->name('pages.show');

// Unduh (Download Center)
Route::get('/unduh', [DownloadController::class, 'index'])->name('downloads.index');
Route::get('/unduh/kategori/{categorySlug}', [DownloadController::class, 'index'])->name('downloads.category');
Route::get('/unduh/file/{slug}', [DownloadController::class, 'download'])->name('downloads.file');

// FAQ & Contoh Kasus
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');
Route::get('/faq/contoh-kasus', [FaqController::class, 'cases'])->name('faq.cases');

// Locale
Route::get('/lang/{locale}', [LanguageController::class, 'swap']);

// Admin Authentication
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('articles', AdminArticleController::class)->except(['show']);
        Route::resource('categories', AdminCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('faqs', AdminFaqController::class)->except(['show']);
        Route::resource('downloads', AdminDownloadController::class)->except(['show']);
        Route::resource('media', AdminMediaItemController::class)->except(['show']);
        Route::resource('inquiries', AdminInquiryController::class)->only(['index', 'update', 'destroy']);
        Route::resource('users', AdminUserController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    });
});
