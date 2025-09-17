<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ComboOffer;
use Illuminate\Http\Request;

class ComboOfferController extends Controller
{
    public function index()
    {
        $combos = ComboOffer::active()
            ->with(['products'])
            ->orderBy('display_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('web.combos.index', compact('combos'));
    }

    public function show($slug)
    {
        $combo = ComboOffer::where('slug', $slug)
            ->with(['products' => function($query) {
                $query->where('is_active', true);
            }])
            ->firstOrFail();
            
        // Check if combo is currently active
        if (!$combo->isCurrentlyActive()) {
            abort(404);
        }

        // Get related combos
        $relatedCombos = ComboOffer::active()
            ->where('id', '!=', $combo->id)
            ->take(4)
            ->get();

        return view('web.combos.show', compact('combo', 'relatedCombos'));
    }
}