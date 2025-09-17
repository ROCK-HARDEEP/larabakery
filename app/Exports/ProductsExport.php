<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct() {}

    public function collection(): Collection
    {
        return Product::query()->with('category')
            ->orderBy('name')
            ->get(['id','name','base_price','stock','is_active','category_id','created_at']);
    }

    public function headings(): array
    {
        return ['ID','Name','Category','Price','Stock','Active','Created At'];
    }

    public function map($p): array
    {
        return [
            $p->id,
            $p->name,
            optional($p->category)->name,
            $p->base_price,
            $p->stock,
            $p->is_active ? 'Yes' : 'No',
            optional($p->created_at)->toDateTimeString(),
        ];
    }
}


