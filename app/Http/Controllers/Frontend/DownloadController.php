<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Download;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    public function index(Request $request, ?string $categorySlug = null): View
    {
        $categories = Category::published()
            ->where('type', 'download')
            ->withCount(['downloads' => fn ($q) => $q->published()])
            ->orderBy('sort_order')
            ->get();

        $activeCategory = $categorySlug
            ? $categories->firstWhere('slug', $categorySlug)
            : null;

        if ($categorySlug && ! $activeCategory) {
            abort(404);
        }

        $search = trim((string) $request->input('q', ''));

        $downloads = Download::published()
            ->with('category')
            ->when($activeCategory, fn ($q) => $q->where('category_id', $activeCategory->id))
            ->when($search !== '', fn ($q) => $q->where(fn ($inner) => $inner
                ->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")))
            ->orderByDesc('download_count')
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        return view('frontend.downloads.index', compact('categories', 'activeCategory', 'downloads', 'search'));
    }

    public function download(string $slug): StreamedResponse|RedirectResponse
    {
        $item = Download::published()->where('slug', $slug)->firstOrFail();
        $item->increment('download_count');

        if ($item->external_url && ! $item->file_path) {
            return redirect()->away($item->external_url);
        }

        if ($item->file_path && Storage::disk('public')->exists($item->file_path)) {
            $name = $item->file_name ?: basename($item->file_path);

            return Storage::disk('public')->download($item->file_path, $name);
        }

        abort(404, 'Berkas dokumen tidak ditemukan.');
    }
}

