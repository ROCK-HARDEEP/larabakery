<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class ProductsImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    protected $imported = 0;
    protected $skipped = 0;
    protected $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $rowIndex => $row) {
            try {
                $result = $this->createProduct($row, $rowIndex + 2); // +2 because row 1 is headers and we start from 0
                if ($result === 'skipped') {
                    $this->skipped++;
                } else {
                    $this->imported++;
                }
            } catch (\Exception $e) {
                $this->errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
            }
        }
    }

    private function createProduct(Collection $row, int $rowNumber)
    {
        // Find category by name or ID
        $category = $this->findCategory($row['category']);
        
        if (!$category) {
            throw new \Exception("Category '{$row['category']}' not found for product '{$row['name']}'");
        }

        $slug = $row['slug'] ?: Str::slug($row['name']);
        
        // Check if product already exists (by name or slug)
        $existingProduct = Product::where('name', $row['name'])
            ->orWhere('slug', $slug)
            ->first();
            
        if ($existingProduct) {
            // Product already exists, skip it
            return 'skipped';
        }

        // Create product
        $product = Product::create([
            'name' => $row['name'],
            'slug' => $slug,
            'description' => $row['description'] ?? null,
            'category_id' => $category->id,
            'base_price' => $row['base_price'],
            'tax_rate' => $row['tax_rate'] ?? 0,
            'stock' => $row['stock'] ?? 0,
            'hsn_code' => $row['hsn_code'] ?? null,
            'is_active' => $this->parseBooleanValue($row['is_active'] ?? true),
            'meta_title' => $row['meta_title'] ?? null,
            'meta_description' => $row['meta_description'] ?? null,
            'meta_keywords' => $row['meta_keywords'] ?? null,
        ]);

        return 'created';
    }

    private function findCategory($categoryIdentifier)
    {
        if (is_numeric($categoryIdentifier)) {
            return Category::find($categoryIdentifier);
        }
        
        return Category::where('name', $categoryIdentifier)
            ->orWhere('slug', Str::slug($categoryIdentifier))
            ->first();
    }

    private function parseBooleanValue($value)
    {
        if (is_bool($value)) {
            return $value;
        }
        
        $value = strtolower(trim($value));
        return in_array($value, ['1', 'true', 'yes', 'active', 'on']);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'stock' => 'nullable|integer|min:0',
            'hsn_code' => 'nullable|string|max:20',
            'slug' => 'nullable|string|max:255',
        ];
    }
    
    public function getImported(): int
    {
        return $this->imported;
    }
    
    public function getSkipped(): int
    {
        return $this->skipped;
    }
    
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'Product name is required',
            'category.required' => 'Category is required',
            'base_price.required' => 'Base price is required',
            'base_price.numeric' => 'Base price must be a number',
        ];
    }
}