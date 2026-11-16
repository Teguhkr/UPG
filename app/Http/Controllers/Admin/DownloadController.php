<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Download;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DownloadController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::where('type', 'download')->orderBy('sort_order')->get();
        $categoryId = $request->input('category_id');
        $search = trim((string) $request->input('q', ''));

        $downloads = Download::with('category')
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.downloads.index', compact('downloads', 'categories', 'categoryId', 'search'));
    }

    public function create(): View
    {
        $categories = Category::where('type', 'download')->orderBy('sort_order')->get();

        return view('admin.downloads.form', [
            'download' => new Download(['is_published' => true]),
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = now();

        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_path'] = $file->storeAs('downloads/uploads', time() . '_' . $file->getClientOriginalName(), 'public');
            $bytes = $file->getSize();
            $data['file_size'] = $bytes > 1048576 ? round($bytes / 1048576, 2) . ' MB' : round(max(1, $bytes) / 1024, 1) . ' KB';
        }

        if (empty($data['file_path']) && empty($data['external_url'])) {
            return back()->withInput()->withErrors(['file_upload' => 'Unggah berkas dokumen atau isi tautan eksternal.']);
        }

        Download::create($data);

        return redirect()->route('admin.downloads.index')
            ->with('success', 'Dokumen unduhan berhasil ditambahkan.');
    }

    public function edit(Download $download): View
    {
        $categories = Category::where('type', 'download')->orderBy('sort_order')->get();

        return view('admin.downloads.form', compact('download', 'categories'));
    }

    public function update(Request $request, Download $download): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('file_upload')) {
            if ($download->file_path && str_starts_with($download->file_path, 'downloads/uploads/')) {
                Storage::disk('public')->delete($download->file_path);
            }
            $file = $request->file('file_upload');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_path'] = $file->storeAs('downloads/uploads', time() . '_' . $file->getClientOriginalName(), 'public');
            $bytes = $file->getSize();
            $data['file_size'] = $bytes > 1048576 ? round($bytes / 1048576, 2) . ' MB' : round(max(1, $bytes) / 1024, 1) . ' KB';
        }

        $download->update($data);

        return redirect()->route('admin.downloads.index')
            ->with('success', 'Dokumen unduhan berhasil diperbarui.');
    }

    public function destroy(Download $download): RedirectResponse
    {
        if ($download->file_path && str_starts_with($download->file_path, 'downloads/uploads/')) {
            Storage::disk('public')->delete($download->file_path);
        }
        $download->delete();

        return redirect()->route('admin.downloads.index')
            ->with('success', 'Dokumen unduhan berhasil dihapus.');
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'external_url' => ['nullable', 'url', 'max:500'],
            'version' => ['nullable', 'string', 'max:64'],
            'file_upload' => ['nullable', 'file', 'max:51200'],
        ]);
    }

    protected function uniqueSlug(string $source): string
    {
        $base = Str::slug($source) ?: 'dokumen';
        if (strlen($base) > 170) {
            $base = substr($base, 0, 170);
        }
        $slug = $base;
        $i = 1;
        while (Download::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}

