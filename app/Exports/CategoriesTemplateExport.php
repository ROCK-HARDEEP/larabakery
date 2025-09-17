<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class CategoriesTemplateExport implements FromArray, WithHeadings, WithColumnWidths, WithChunkReading
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
            'Parent Category',
            'Position',
            'Is Active',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Name
            'B' => 20, // Slug
            'C' => 40, // Description
            'D' => 20, // Parent Category
            'E' => 12, // Position
            'F' => 12, // Is Active
        ];
    }

    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows at a time
    }
}