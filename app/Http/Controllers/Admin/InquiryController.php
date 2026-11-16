<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status');
        $inquiries = Inquiry::when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.inquiries.index', compact('inquiries', 'status'));
    }

    public function update(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:baru,diproses,selesai'],
            'admin_notes' => ['nullable', 'string'],
        ]);

        $inquiry->update([
            'status' => $data['status'],
            'admin_notes' => $data['admin_notes'] ?? null,
            'responded_at' => $data['status'] === 'selesai' ? now() : $inquiry->responded_at,
        ]);

        return redirect()->route('admin.inquiries.index')
            ->with('success', 'Status konsultasi berhasil diperbarui.');
    }

    public function destroy(Inquiry $inquiry): RedirectResponse
    {
        $inquiry->delete();

        return redirect()->route('admin.inquiries.index')
            ->with('success', 'Pesan konsultasi berhasil dihapus.');
    }
}

