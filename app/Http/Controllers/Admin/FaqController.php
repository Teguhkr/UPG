<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->input('type');
        $search = trim((string) $request->input('q', ''));

        $faqs = Faq::when($type, fn ($q) => $q->where('category_type', $type))
            ->when($search !== '', fn ($q) => $q->where('question', 'like', "%{$search}%"))
            ->orderBy('category_type')
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        return view('admin.faqs.index', compact('faqs', 'type', 'search'));
    }

    public function create(): View
    {
        return view('admin.faqs.form', [
            'faq' => new Faq(['category_type' => 'faq', 'is_published' => true, 'sort_order' => 0]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_type' => ['required', 'in:faq,contoh_kasus'],
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $base = Str::slug($data['question']);
        if (strlen($base) > 170) {
            $base = substr($base, 0, 170);
        }
        $slug = $base ?: 'faq';
        $i = 1;
        while (Faq::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        Faq::create([
            'category_type' => $data['category_type'],
            'question' => $data['question'],
            'slug' => $slug,
            'answer' => $data['answer'],
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_published' => $request->boolean('is_published', true),
        ]);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'Item FAQ / Contoh Kasus berhasil ditambahkan.');
    }

    public function edit(Faq $faq): View
    {
        return view('admin.faqs.form', compact('faq'));
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $data = $request->validate([
            'category_type' => ['required', 'in:faq,contoh_kasus'],
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $faq->update([
            'category_type' => $data['category_type'],
            'question' => $data['question'],
            'answer' => $data['answer'],
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'Item FAQ / Contoh Kasus berhasil diperbarui.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')
            ->with('success', 'Item FAQ / Contoh Kasus berhasil dihapus.');
    }
}

