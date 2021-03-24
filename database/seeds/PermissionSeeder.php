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
            
            $distinctModels = [
            	'ApplicationSetting',
            	'Asset',
            	'Dispatch',
            	'Manager',
            	'Merchant',
            	// 'MerchantDeal',
            	// 'PackagingPackage',
            	// 'DeliveryCompany',
            	'Permission',
            	'Product',
            	'ProductCategory',
            	'ProductStock',
            	'Requisition',
            	'Role',
            	'Warehouse',
            	// 'WarehouseDeliveryCompany'
            	// 'WarehouseManager'
            	'WarehouseOwner',
            	'WarehouseProduct',
            ];

            foreach ($distinctModels as $distinctModel) {
            	
            	Permission::insert([
                
                    [ 'name' => 'create-'.strtolower($distinctModel) ],
                    [ 'name' => 'view-'.strtolower($distinctModel).'-index' ],
                    [ 'name' => 'update-'.strtolower($distinctModel) ],
                    [ 'name' => 'delete-'.strtolower($distinctModel) ]

                ]);

            } 

        }

    }
}
