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
            
            $modelCRUDableAndApproveable = [
                'Product-Stock',
            ];

            $modelsCRUDable = [
            	'Asset',
            	'Manager',
            	'Merchant',
            	// 'MerchantDeal',
            	// 'PackagingPackage',
            	// 'DeliveryCompany',
            	'Product-Category',
            	'Product',
            	'Role',
            	'Warehouse-Owner',
            	'Warehouse',
            	// 'WarehouseDeliveryCompany'
            	// 'WarehouseManager'
            	// 'WarehouseProduct',
            ];

            $modelsViewableAndUpdatable = [
                'Application-Setting',  // view / update
                'Requisition', // view / update(cancel)
            ];

            $modelsViewableAndMakeable = [
                'Dispatch',  // view / make
            ];

            $modelsViewable = [
                'Permission',  // view
            ];

            foreach ($modelCRUDableAndApproveable as $model) {
                
                Permission::insert([
                
                    [ 'name' => 'create-'.strtolower($model) ],
                    [ 'name' => 'view-'.strtolower($model).'-index' ],
                    [ 'name' => 'update-'.strtolower($model) ],
                    [ 'name' => 'delete-'.strtolower($model) ],
                    [ 'name' => 'approve-'.strtolower($model) ]

                ]);

            }

            foreach ($modelsCRUDable as $model) {
            	
            	Permission::insert([
                
                    [ 'name' => 'create-'.strtolower($model) ],
                    [ 'name' => 'view-'.strtolower($model).'-index' ],
                    [ 'name' => 'update-'.strtolower($model) ],
                    [ 'name' => 'delete-'.strtolower($model) ]

                ]);

            }

            foreach ($modelsViewableAndUpdatable as $model) {
                
                Permission::insert([
                
                    [ 'name' => 'view-'.strtolower($model).'-index' ],
                    [ 'name' => 'update-'.strtolower($model) ],

                ]);

            }

            foreach ($modelsViewableAndMakeable as $model) {
                
                Permission::insert([
                
                    [ 'name' => 'view-'.strtolower($model).'-index' ],
                    [ 'name' => 'make-'.strtolower($model) ],

                ]);

            }

            foreach ($modelsViewable as $model) {
                
                Permission::insert([
                
                    [ 'name' => 'view-'.strtolower($model).'-index' ],

                ]);

            } 

        }

    }
}
