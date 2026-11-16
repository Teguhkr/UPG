<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\MediaItem;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function infografis(): View
    {
        $items = MediaItem::published()
            ->where('type', 'infografis')
            ->orderBy('sort_order')
            ->get();

        return view('frontend.media.infografis', compact('items'));
    }

    public function videos(): View
    {
        $items = MediaItem::published()
            ->where('type', 'video')
            ->orderBy('sort_order')
            ->get();

        return view('frontend.media.videos', compact('items'));
    }
}

