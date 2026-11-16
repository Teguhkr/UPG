<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q', ''));

        $faqs = Faq::published()
            ->where('category_type', 'faq')
            ->when($search !== '', fn ($q) => $q->where(fn ($inner) => $inner
                ->where('question', 'like', "%{$search}%")
                ->orWhere('answer', 'like', "%{$search}%")))
            ->orderBy('sort_order')
            ->get();

        return view('frontend.faq.index', [
            'items' => $faqs,
            'search' => $search,
            'mode' => 'faq',
            'pageTitle' => "FAQ's (Pertanyaan yang Sering Diajukan)",
            'pageSubtitle' => 'Jawaban resmi seputar pengendalian gratifikasi, tata cara pelaporan, dan ketentuan hukum di lingkungan KKP.',
        ]);
    }

    public function cases(Request $request): View
    {
        $search = trim((string) $request->input('q', ''));

        $cases = Faq::published()
            ->where('category_type', 'contoh_kasus')
            ->when($search !== '', fn ($q) => $q->where(fn ($inner) => $inner
                ->where('question', 'like', "%{$search}%")
                ->orWhere('answer', 'like', "%{$search}%")))
            ->orderBy('sort_order')
            ->get();

        return view('frontend.faq.index', [
            'items' => $cases,
            'search' => $search,
            'mode' => 'contoh_kasus',
            'pageTitle' => 'Contoh Kasus Gratifikasi',
            'pageSubtitle' => 'Studi kasus nyata dan panduan menyikapi situasi gratifikasi di unit kerja maupun pelayanan publik.',
        ]);
    }
}

