<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function download(Invoice $invoice)
    {
        abort_unless($invoice->order->user_id === auth()->id(), 403);
        if ($invoice->pdf_path && Storage::disk('public')->exists($invoice->pdf_path)) {
            return response()->download(Storage::disk('public')->path($invoice->pdf_path));
        }
        return back()->withErrors(['invoice'=>'Invoice PDF not available yet']);
    }
}