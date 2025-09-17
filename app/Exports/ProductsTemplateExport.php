<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductsTemplateExport implements FromArray, WithHeadings, WithColumnWidths, WithChunkReading
{
    public function array(): array
    {
        // Return empty array to provide only headers without sample data
        return [];
    }

    public function headings(): array
    {
        return [
            'Name *',
            'Slug',
            'Description',
            'Category *',
            'Base Price *',
            'Tax Rate (%)',
            'Stock',
            'HSN Code',
            'Is Active',
            'Meta Title',
            'Meta Description',
            'Meta Keywords',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Name
            'B' => 20, // Slug
            'C' => 30, // Description
            'D' => 15, // Category
            'E' => 12, // Base Price
            'F' => 12, // Tax Rate
            'G' => 10, // Stock
            'H' => 12, // HSN Code
            'I' => 10, // Is Active
            'J' => 20, // Meta Title
            'K' => 30, // Meta Description
            'L' => 25, // Meta Keywords
        ];
    }

    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows at a time
    }
}