<?php

namespace App\Imports;

use App\Models\ProductCategory;
// use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
// use Maatwebsite\Excel\Concerns\ToCollection;

class ProductCategoriesImport implements ToModel, WithValidation, WithHeadingRow
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',   
            'is_perishable' => 'required|boolean',          
            'parent_category_name' => 'nullable|string|max:100',   
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'name.required' => 'Category name is required.',
            'name.max' => 'Category name is out of max length.',
            'name.*' => 'Category name is invalid.',
            
            'is_perishable.boolean' => 'Perishable value should be boolean.',
            'is_perishable.*' => 'Perishable value is required.',

            'parent_category_name.required' => 'Parent category is required.',
            'parent_category_name.max' => 'Parent category is out of max length.',
            'parent_category_name.*' => 'Parent category is invalid.',
        ];
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {   
        // parent category
        if (! empty($row['parent_category_name']) && preg_match("/[a-z]/i", $row['parent_category_name']) && strlen($row['parent_category_name']) > 2) {
            
            $parentCategory = ProductCategory::withTrashed()->firstOrNew(
                [
                    'name' => strtolower($row['parent_category_name']),
                ]
            );

            $parentCategory->save();

        }

        return ProductCategory::withTrashed()->updateOrCreate(
            [
                'name'     => strtolower($row['name']),
            ],
            [
                'is_perishable'    => ((isset($parentCategory) && $parentCategory->is_perishable) ? $parentCategory->is_perishable :  filter_var($row['is_perishable'], FILTER_VALIDATE_BOOLEAN)), 
                'parent_category_id' => isset($parentCategory) ? $parentCategory->id : NULL,
                'deleted_at' => NULL
            ]
        );
    }
}
