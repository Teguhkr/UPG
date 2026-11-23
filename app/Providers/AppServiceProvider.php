<?php

namespace App\Providers;

use App\Models\Inquiry;
use App\Models\SiteSetting;
use Illuminate\Filesystem\LocalFilesystemAdapter;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Local\LocalFilesystemAdapter as LeagueLocalAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\Visibility;
use League\MimeTypeDetection\ExtensionMimeTypeDetector;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Fallback untuk hosting tanpa ekstensi PHP "fileinfo":
        // deteksi MIME type berdasarkan ekstensi file, bukan class finfo.
        if (!class_exists('finfo')) {
            Storage::extend('local', function ($app, array $config) {
                $visibility = PortableVisibilityConverter::fromArray(
                    $config['permissions'] ?? [],
                    $config['directory_visibility'] ?? $config['visibility'] ?? Visibility::PRIVATE
                );

                $adapter = new LeagueLocalAdapter(
                    $config['root'],
                    $visibility,
                    $config['lock'] ?? LOCK_EX,
                    ($config['links'] ?? null) === 'skip'
                    ? LeagueLocalAdapter::SKIP_LINKS
                    : LeagueLocalAdapter::DISALLOW_LINKS,
                    new ExtensionMimeTypeDetector
                );

                return new LocalFilesystemAdapter(new Flysystem($adapter, $config), $adapter, $config);
            });
        }
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer('*', function ($view) {
            if (Schema::hasTable('site_settings')) {
                $view->with('siteSettings', SiteSetting::allKeyed());
            } else {
                $view->with('siteSettings', []);
            }

            if (str_starts_with($view->getName(), 'admin.') || str_starts_with($view->getName(), 'layouts.sections.menu')) {
                $newInquiriesCount = Schema::hasTable('inquiries')
                    ? Inquiry::where('status', 'baru')->count()
                    : 0;
                $view->with('newInquiriesCount', $newInquiriesCount);
            }
        });
    }
}
