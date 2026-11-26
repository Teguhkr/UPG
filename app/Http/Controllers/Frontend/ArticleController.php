<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\MediaItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function tentangUpg(): View
    {
        $article = Article::published()
            ->where('slug', 'tentang-upg')
            ->firstOrFail();

        $article->increment('hits');

        $gallery = MediaItem::published()
            ->where('type', 'gallery')
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        return view('frontend.articles.page', [
            'article' => $article,
            'pageTitle' => 'Tentang UPG KKP',
            'breadcrumbs' => [
                ['label' => 'Beranda', 'url' => route('home')],
                ['label' => 'Profil', 'url' => null],
                ['label' => 'Tentang UPG', 'url' => null],
            ],
            'gallery' => $gallery,
        ]);
    }

    public function byCategory(Request $request, string $categorySlug): View
    {
        $category = Category::published()
            ->where('slug', $categorySlug)
            ->where('type', 'article')
            ->firstOrFail();

        $search = trim((string) $request->input('q', ''));

        $articles = Article::published()
            ->with('category')
            ->where('category_id', $category->id)
            ->when($search !== '', fn ($q) => $q->where(fn ($inner) => $inner
                ->where('title', 'like', "%{$search}%")
                ->orWhere('excerpt', 'like', "%{$search}%")))
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('frontend.articles.index', compact('category', 'articles', 'search'));
    }

    public function show(string $slug): View
    {
        $article = Article::published()
            ->with(['category', 'author'])
            ->where('slug', $slug)
            ->firstOrFail();

        $article->increment('hits');

        $related = Article::published()
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->orderByDesc('published_at')
            ->take(4)
            ->get();

        $previousQuery = Article::published()->where(function ($query) use ($article) {
            if ($article->published_at === null) {
                $query->where(function ($query) use ($article) {
                    $query->whereNull('published_at')->where('id', '>', $article->id);
                })->orWhereNotNull('published_at');
            } else {
                $query->where('published_at', '>', $article->published_at)
                    ->orWhere(function ($query) use ($article) {
                        $query->where('published_at', $article->published_at)
                            ->where('id', '>', $article->id);
                    });
            }
        });

        $previous = $article->published_at === null
            ? $previousQuery->orderByRaw('published_at IS NULL DESC')->orderByDesc('published_at')->orderBy('id')->first()
            : $previousQuery->orderBy('published_at')->orderBy('id')->first();

        $nextQuery = Article::published()->where(function ($query) use ($article) {
            if ($article->published_at === null) {
                $query->whereNull('published_at')->where('id', '<', $article->id);
            } else {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<', $article->published_at)
                    ->orWhere(function ($query) use ($article) {
                        $query->where('published_at', $article->published_at)
                            ->where('id', '<', $article->id);
                    });
            }
        });

        $next = $article->published_at === null
            ? $nextQuery->orderByDesc('id')->first()
            : $nextQuery->orderByRaw('published_at IS NULL ASC')->orderByDesc('published_at')->orderByDesc('id')->first();

        return view('frontend.articles.show', compact('article', 'related', 'previous', 'next'));
    }

    public function page(string $slug): View
    {
        $article = Article::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $article->increment('hits');

        return view('frontend.articles.page', [
            'article' => $article,
            'pageTitle' => $article->title,
            'breadcrumbs' => [
                ['label' => 'Beranda', 'url' => route('home')],
                ['label' => $article->title, 'url' => null],
            ],
            'gallery' => collect(),
        ]);
    }
}

