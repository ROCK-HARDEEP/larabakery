<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private ?string $startDate = null, private ?string $endDate = null) {}

    public function collection(): Collection
    {
        $q = Order::with('user');
        if ($this->startDate) $q->whereDate('created_at', '>=', $this->startDate);
        if ($this->endDate) $q->whereDate('created_at', '<=', $this->endDate);
        return $q->orderBy('created_at')->get();
    }

    public function headings(): array
    {
        return ['Order #','Customer','Status','Payment','Currency','Subtotal','Tax','Discount','Shipping','Total','Placed At'];
    }

    public function map($o): array
    {
        return [
            $o->id,
            optional($o->user)->name,
            $o->status,
            $o->payment_status,
            $o->currency,
            $o->subtotal,
            $o->tax,
            $o->discount,
            $o->shipping_fee,
            $o->total,
            optional($o->created_at)->toDateTimeString(),
        ];
    }
}


