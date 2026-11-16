<?php

namespace App\Providers;

use App\Models\Inquiry;
use App\Models\SiteSetting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
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
