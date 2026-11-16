<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('frontend.contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'unit_kerja' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'subject.required' => 'Subjek konsultasi wajib diisi.',
            'message.required' => 'Pesan konsultasi wajib diisi.',
            'message.min' => 'Pesan konsultasi minimal 10 karakter.',
        ]);

        Inquiry::create($validated);

        return redirect()
            ->route('contact.index')
            ->with('success', 'Pertanyaan / konsultasi Anda telah dikirim dan tercatat di UPG KKP. Tim kami akan segera menindaklanjuti.');
    }
}

