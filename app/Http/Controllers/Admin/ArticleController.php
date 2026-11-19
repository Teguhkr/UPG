<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::where('type', 'article')->orderBy('sort_order')->get();
        $search = trim((string) $request->input('q', ''));
        $categoryId = $request->input('category_id');

        $articles = Article::with(['category', 'author'])
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.articles.index', compact('articles', 'categories', 'search', 'categoryId'));
    }

    public function create(): View
    {
        $categories = Category::where('type', 'article')->orderBy('sort_order')->get();

        return view('admin.articles.form', [
            'article' => new Article(['is_published' => true, 'published_at' => now()]),
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['title']);
        $data['author_id'] = Auth::id();
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['published_at'] ?? now();

        if ($request->hasFile('featured_image_file')) {
            $data['featured_image'] = $request->file('featured_image_file')
                ->store('upg-media/uploads/articles', 'public');
        }

        if (empty($data['excerpt']) && ! empty($data['content'])) {
            $data['excerpt'] = Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($data['content']))), 220);
        }

        Article::create($data);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil ditambahkan.');
    }

    public function edit(Article $article): View
    {
        $categories = Category::where('type', 'article')->orderBy('sort_order')->get();

        return view('admin.articles.form', compact('article', 'categories'));
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $data = $this->validatedData($request, $article->id);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['title'], $article->id);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('featured_image_file')) {
            if ($article->featured_image && str_starts_with($article->featured_image, 'upg-media/uploads/')) {
                Storage::disk('public')->delete($article->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image_file')
                ->store('upg-media/uploads/articles', 'public');
        }

        if (empty($data['excerpt']) && ! empty($data['content'])) {
            $data['excerpt'] = Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($data['content']))), 220);
        }

        $article->update($data);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        if ($article->featured_image && str_starts_with($article->featured_image, 'upg-media/uploads/')) {
            Storage::disk('public')->delete($article->featured_image);
        }
        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }

    public function destroyFeaturedImage(Article $article): RedirectResponse
    {
        if ($article->featured_image && str_starts_with($article->featured_image, 'upg-media/uploads/')) {
            Storage::disk('public')->delete($article->featured_image);
        }

        $article->update(['featured_image' => null]);

        return redirect()->back()
            ->with('success', 'Gambar sampul artikel berhasil dihapus.');
    }

    protected function validatedData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'published_at' => ['nullable', 'date'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'featured_image_file' => ['nullable', 'image', 'max:5120'],
        ]);
    }

    protected function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source) ?: 'artikel';
        $slug = $base;
        $i = 1;

        while (Article::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}

