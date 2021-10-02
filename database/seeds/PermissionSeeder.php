<?php

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        if (Permission::all()->isEmpty()) {
            
            /*
                $modelCRUDableAndApproveable = [
                    'Product-Stock',
                ];
            */
           
            $modelsCRUDable = [
            	'Role',
            	'Product',
            	'Manager',
            	'Merchant',
                'Warehouse',
            	'Product-Asset',
                'Product-Stock',
            	'Warehouse-Owner',
                'Warehouse-Asset',
            	// 'WarehouseProduct',   // Product-Stock
                // 'Product-Category',  // Product-Asset
            	// 'Product-Manufacturer',  // Product-Asset
            	// 'WarehouseDeliveryCompany'
                'Merchant-Deal',
                'Merchant-Product',
                'Merchant-Payment',
            ];

            $modelsCreateableUpdatableAndDeletable = [
                'Logistic-Asset',
                // 'Delivery-Company',      // Logistic
                // 'Packaging-Package'     // Logistic
            ];            

            $modelsViewableAndUpdatable = [
                'Requisition', // view / update(cancel)
                'Application-Setting',  // view / update
            ];

            $modelsViewableRecommendableAndApproveable = [
                'Dispatch',  // view / make
            ];

            $modelsViewable = [
                'Permission',  // view
            ];

            $modelsViewable2 = [
                'General-Dashboard-One',  // view
                'General-Dashboard-Two',  // view
            ];

            /*
                foreach ($modelCRUDableAndApproveable as $model) {
                    
                    Permission::insert([
                    
                        [ 'name' => 'create-'.strtolower($model) ],
                        [ 'name' => 'view-'.strtolower($model).'-index' ],
                        [ 'name' => 'update-'.strtolower($model) ],
                        [ 'name' => 'delete-'.strtolower($model) ],
                        // [ 'name' => 'approve-'.strtolower($model) ]

                    ]);

                }
            */

            foreach ($modelsCRUDable as $model) {
                
                Permission::insert([
                
                    [ 'name' => 'create-'.strtolower($model) ],
                    [ 'name' => 'view-'.strtolower($model).'-index' ],
                    [ 'name' => 'update-'.strtolower($model) ],
                    [ 'name' => 'delete-'.strtolower($model) ]

                ]);

            }

            foreach ($modelsCreateableUpdatableAndDeletable as $model) {
                
                Permission::insert([
                
                    [ 'name' => 'create-'.strtolower($model) ],
                    [ 'name' => 'update-'.strtolower($model) ],
                    [ 'name' => 'delete-'.strtolower($model) ],

                ]);

            }

            foreach ($modelsViewableAndUpdatable as $model) {
                
                Permission::insert([
                
                    [ 'name' => 'view-'.strtolower($model).'-index' ],
                    [ 'name' => 'update-'.strtolower($model) ],

                ]);

            }

            foreach ($modelsViewableRecommendableAndApproveable as $model) {
                
                Permission::insert([
                
                    [ 'name' => 'view-'.strtolower($model).'-index' ],
                    [ 'name' => 'recommend-'.strtolower($model) ],
                    [ 'name' => 'approve-'.strtolower($model) ],

                ]);

            }

            foreach ($modelsViewable as $model) {
                
                Permission::insert([
                
                    [ 'name' => 'view-'.strtolower($model).'-index' ],

                ]);

            }

            foreach ($modelsViewable2 as $model) {
                
                Permission::insert([
                
                    [ 'name' => 'view-'.strtolower($model) ],

                ]);

            } 

        }

    }
}
