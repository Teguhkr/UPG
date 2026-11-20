<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $nextOrder = ((int) Faq::where('category_type', 'faq')->max('sort_order')) + 1;

        return view('admin.faqs.form', [
            'faq' => new Faq(['category_type' => 'faq', 'is_published' => true, 'sort_order' => $nextOrder]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_type' => ['required', 'in:faq,contoh_kasus'],
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:1'],
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

        DB::transaction(function () use ($request, $data, $slug): void {
            $desiredOrder = isset($data['sort_order']) ? (int) $data['sort_order'] : null;

            $faq = Faq::create([
                'category_type' => $data['category_type'],
                'question' => $data['question'],
                'slug' => $slug,
                'answer' => $data['answer'],
                'sort_order' => $desiredOrder ?? 0,
                'is_published' => $request->boolean('is_published', true),
            ]);

            $this->reorderCategory($data['category_type'], $faq, $desiredOrder ?? (int) $faq->sort_order);
        });

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
            'sort_order' => ['nullable', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($request, $faq, $data): void {
            $previousCategory = $faq->category_type;
            $desiredOrder = isset($data['sort_order']) ? (int) $data['sort_order'] : null;

            $faq->update([
                'category_type' => $data['category_type'],
                'question' => $data['question'],
                'answer' => $data['answer'],
                'sort_order' => $desiredOrder ?? $faq->sort_order,
                'is_published' => $request->boolean('is_published'),
            ]);

            $this->reorderCategory($data['category_type'], $faq, $desiredOrder ?? (int) $faq->sort_order);

            if ($previousCategory !== $data['category_type']) {
                $this->reorderCategory($previousCategory);
            }
        });

        return redirect()->route('admin.faqs.index')
            ->with('success', 'Item FAQ / Contoh Kasus berhasil diperbarui.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $categoryType = $faq->category_type;

        DB::transaction(function () use ($faq, $categoryType): void {
            $faq->delete();
            $this->reorderCategory($categoryType);
        });

        return redirect()->route('admin.faqs.index')
            ->with('success', 'Item FAQ / Contoh Kasus berhasil dihapus.');
    }

    /**
     * Re-sequence sort_order (1..N) for a category_type, placing the prioritized
     * item at the requested position so existing items shift down cleanly.
     */
    protected function reorderCategory(string $categoryType, ?Faq $prioritized = null, ?int $desiredOrder = null): void
    {
        $items = Faq::where('category_type', $categoryType)
            ->when($prioritized, fn ($q) => $q->where('id', '!=', $prioritized->id))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->values();

        if ($prioritized) {
            $targetPos = $desiredOrder !== null
                ? max(1, min($desiredOrder, $items->count() + 1))
                : $items->count() + 1;

            $items->splice($targetPos - 1, 0, [$prioritized]);
        }

        foreach ($items as $index => $item) {
            $pos = $index + 1;
            if ((int) $item->sort_order !== $pos) {
                Faq::whereKey($item->id)->update(['sort_order' => $pos]);
            }
        }
    }
}

