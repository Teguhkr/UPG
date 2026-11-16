<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MediaItemController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->input('type');

        $items = MediaItem::when($type, fn ($q) => $q->where('type', $type))
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(16)
            ->withQueryString();

        return view('admin.media.index', compact('items', 'type'));
    }

    public function create(): View
    {
        return view('admin.media.form', [
            'mediaItem' => new MediaItem(['type' => 'infografis', 'is_published' => true, 'sort_order' => 0]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        $base = Str::slug($data['title']) ?: 'media';
        $slug = $base;
        $i = 1;
        while (MediaItem::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        $data['slug'] = $slug;

        if ($request->hasFile('media_file')) {
            $data['file_path'] = $request->file('media_file')->store('upg-media/uploads/media', 'public');
        }

        if ($request->hasFile('poster_file')) {
            $data['poster_path'] = $request->file('poster_file')->store('upg-media/uploads/posters', 'public');
        }

        MediaItem::create($data);

        return redirect()->route('admin.media.index')
            ->with('success', 'Item media berhasil ditambahkan.');
    }

    public function edit(MediaItem $media): View
    {
        return view('admin.media.form', ['mediaItem' => $media]);
    }

    public function update(Request $request, MediaItem $media): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        if ($request->hasFile('media_file')) {
            if ($media->file_path && str_starts_with($media->file_path, 'upg-media/uploads/')) {
                Storage::disk('public')->delete($media->file_path);
            }
            $data['file_path'] = $request->file('media_file')->store('upg-media/uploads/media', 'public');
        }

        if ($request->hasFile('poster_file')) {
            if ($media->poster_path && str_starts_with($media->poster_path, 'upg-media/uploads/')) {
                Storage::disk('public')->delete($media->poster_path);
            }
            $data['poster_path'] = $request->file('poster_file')->store('upg-media/uploads/posters', 'public');
        }

        $media->update($data);

        return redirect()->route('admin.media.index')
            ->with('success', 'Item media berhasil diperbarui.');
    }

    public function destroy(MediaItem $media): RedirectResponse
    {
        if ($media->file_path && str_starts_with($media->file_path, 'upg-media/uploads/')) {
            Storage::disk('public')->delete($media->file_path);
        }
        if ($media->poster_path && str_starts_with($media->poster_path, 'upg-media/uploads/')) {
            Storage::disk('public')->delete($media->poster_path);
        }
        $media->delete();

        return redirect()->route('admin.media.index')
            ->with('success', 'Item media berhasil dihapus.');
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'type' => ['required', 'in:infografis,video,gallery'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'album' => ['nullable', 'string', 'max:150'],
            'embed_url' => ['nullable', 'url', 'max:500'],
            'sort_order' => ['nullable', 'integer'],
            'media_file' => ['nullable', 'file', 'max:102400'],
            'poster_file' => ['nullable', 'image', 'max:5120'],
        ]);
    }
}

