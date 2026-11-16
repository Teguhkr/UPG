<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Download;
use App\Models\Faq;
use App\Models\MediaItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $informasiCategory = Category::where('slug', 'informasi')->first();
        $mengenalCategory = Category::where('slug', 'mengenal-gratifikasi')->first();

        $latestArticles = Article::published()
            ->with('category')
            ->when($informasiCategory, fn ($q) => $q->where('category_id', $informasiCategory->id))
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->take(6)
            ->get();

        $mengenalArticles = Article::published()
            ->with('category')
            ->when($mengenalCategory, fn ($q) => $q->where('category_id', $mengenalCategory->id))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->take(4)
            ->get();

        $faqs = Faq::published()
            ->where('category_type', 'faq')
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        $contohKasus = Faq::published()
            ->where('category_type', 'contoh_kasus')
            ->orderBy('sort_order')
            ->take(5)
            ->get();

        $infografis = MediaItem::published()
            ->where('type', 'infografis')
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $videos = MediaItem::published()
            ->where('type', 'video')
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        $popularDownloads = Download::published()
            ->with('category')
            ->orderByDesc('download_count')
            ->take(5)
            ->get();

        $stats = [
            'articles' => Article::published()->count(),
            'downloads' => Download::published()->count(),
            'total_download_hits' => Download::sum('download_count'),
            'faqs' => Faq::published()->count(),
        ];

        return view('frontend.home', compact(
            'latestArticles',
            'mengenalArticles',
            'faqs',
            'contohKasus',
            'infografis',
            'videos',
            'popularDownloads',
            'stats'
        ));
    }

    public function search(Request $request): View
    {
        $query = trim((string) $request->input('q', ''));

        $articles = collect();
        $downloads = collect();
        $faqs = collect();
        $media = collect();

        if ($query !== '') {
            $articles = Article::published()
                ->with('category')
                ->where(fn ($q) => $q->where('title', 'like', "%{$query}%")
                    ->orWhere('excerpt', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%"))
                ->orderByDesc('published_at')
                ->take(15)
                ->get();

            $downloads = Download::published()
                ->with('category')
                ->where(fn ($q) => $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%"))
                ->orderByDesc('download_count')
                ->take(15)
                ->get();

            $faqs = Faq::published()
                ->where(fn ($q) => $q->where('question', 'like', "%{$query}%")
                    ->orWhere('answer', 'like', "%{$query}%"))
                ->orderBy('sort_order')
                ->take(15)
                ->get();

            $media = MediaItem::published()
                ->where(fn ($q) => $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%"))
                ->orderBy('sort_order')
                ->take(12)
                ->get();
        }

        return view('frontend.search', compact('query', 'articles', 'downloads', 'faqs', 'media'));
    }
}

