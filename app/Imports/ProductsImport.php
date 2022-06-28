<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Variation;
use App\Models\ProductCategory;
use Illuminate\Support\Collection;
// use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToCollection, WithValidation, WithHeadingRow
{
    public function rules(): array
    {
        return [
            'product_category_name' => 'required|string|max:100',
            'name' => 'required|string|max:255',
            'quantity_type' => 'required|string|max:255',
            'has_variations' => 'required|boolean',
            'has_serials' => 'required|boolean',
            'variations' => 'required_if:has_variations,1',
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'product_category_name.required' => 'Category name is required.',
            'product_category_name.max' => 'Category name is out of max length.',
            'product_category_name.*' => 'Category name is invalid.',
            'name.required' => 'Product name is required.',
            'name.max' => 'Product name is out of max length.',
            'name.*' => 'Product name is invalid.',
            'quantity_type.required' => 'Quantity type is required.',
            'quantity_type.max' => 'Quantity type is out of max length.',
            'quantity_type.*' => 'Quantity type is invalid.',
            'variations.required_if' => 'Variation name is required.',
            // 'variations.*' => 'Variation name is invalid.',
        ];
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            if (! isset($row["product_category_name"]) || !isset($row["name"])) {
                return null;
            }

            $newProductCategory = ProductCategory::firstOrCreate(
                ['name'     => strtolower($row['product_category_name'])],
                [
                    'parent_category_id'    => NULL,
                    'deleted_at' => NULL
                ]
            );

            $newProduct = Product::firstOrCreate(
                ['name'     => strtolower($row['name'])],
                [
                    // 'preview'    => $row['preview'] ?? null,
                    'quantity_type' => $row['quantity_type'],
                    'has_variations' => (isset($row['variations']) && count(explode(',', $row['variations'])) > 1) ? $row['has_variations'] : 0,
                    'has_serials' => $row['has_serials'],
                    'product_category_id' => $newProductCategory->id,
                    'deleted_at' => NULL
                ]
            );

            if ($newProduct->has_variations) {

                foreach (explode(',', $row['variations']) as $variationKey => $variation) {

                    $variation = Variation::firstWhere('name', strtolower($variation));

                    // if product has no variaton or available variation type is same as this variation
                    if ($variation && (! $newProduct->variations()->exists() || ($newProduct->variations->first()->variation->variation_type_id == $variation->variation_type_id))) {
                        
                        $newProduct->variations()->firstOrCreate(
                            [ 'variation_id' => $variation->id ],
                            [
                                // 'preview' => NULL
                            ]
                        );

                    }

                }

                // checking product has varation but if no product-variations has been added
                if (! $newProduct->variations->count()) {
                    
                    $newProduct->update([

                        'has_variations' => false

                    ]);

                }

            }

        }
    }
}
