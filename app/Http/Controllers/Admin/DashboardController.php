<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Download;
use App\Models\Faq;
use App\Models\Inquiry;
use App\Models\MediaItem;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'articles' => Article::count(),
            'published_articles' => Article::published()->count(),
            'downloads' => Download::count(),
            'total_download_hits' => Download::sum('download_count'),
            'faqs' => Faq::count(),
            'media' => MediaItem::count(),
            'new_inquiries' => Inquiry::where('status', 'baru')->count(),
            'total_article_hits' => Article::sum('hits'),
        ];

        $categoryChart = Category::where('type', 'article')
            ->withCount('articles')
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($c) => [
                'name' => $c->name,
                'count' => $c->articles_count,
            ]);

        $topDownloads = Download::with('category')
            ->orderByDesc('download_count')
            ->take(6)
            ->get();

        $recentArticles = Article::with('category')
            ->orderByDesc('updated_at')
            ->take(6)
            ->get();

        $recentInquiries = Inquiry::latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'categoryChart',
            'topDownloads',
            'recentArticles',
            'recentInquiries'
        ));
    }
}

