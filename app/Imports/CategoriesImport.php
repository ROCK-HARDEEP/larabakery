<?php

namespace App\Imports;

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

class CategoriesImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    protected $imported = 0;
    protected $skipped = 0;
    protected $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $rowIndex => $row) {
            try {
                $result = $this->createCategory($row, $rowIndex + 2); // +2 because row 1 is headers and we start from 0
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

    private function createCategory(Collection $row, int $rowNumber)
    {
        // Find parent category if specified
        $parentCategory = null;
        if (!empty($row['parent_category'])) {
            $parentCategory = $this->findCategory($row['parent_category']);
            if (!$parentCategory) {
                throw new \Exception("Parent category '{$row['parent_category']}' not found for category '{$row['name']}'");
            }
        }

        $slug = $row['slug'] ?: Str::slug($row['name']);
        
        // Check if category already exists (by name or slug)
        $existingCategory = Category::where('name', $row['name'])
            ->orWhere('slug', $slug)
            ->first();
            
        if ($existingCategory) {
            // Category already exists, skip it
            return 'skipped';
        }

        // Create category
        $category = Category::create([
            'name' => $row['name'],
            'slug' => $slug,
            'description' => $row['description'] ?? null,
            'parent_id' => $parentCategory ? $parentCategory->id : null,
            'position' => $row['position'] ?? 0,
            'is_active' => $this->parseBooleanValue($row['is_active'] ?? true),
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
            'slug' => 'nullable|string|max:255',
            'position' => 'nullable|integer|min:0',
            'parent_category' => 'nullable|string',
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
            'name.required' => 'Category name is required',
        ];
    }
}