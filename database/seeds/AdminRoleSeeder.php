<?php

use App\Models\Role;
use App\Models\Admin;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allAdmins = Admin::all();

        if ($allAdmins->isEmpty()) {
        	
        	$createAdmin = Admin::create([
        		'first_name' => 'Mr.',
        		'last_name' => 'Admin',
        		'user_name' => 'admin-1',
        		'email' => 'admin-1@email.com',
        		'mobile' => '01622888990',
        		'password' => Hash::make('admin'),
        	]);

        	$allAdmins->push($createAdmin);

        }

        // Retrieve Role by name or instantiate a new Role instance...
		$role = Role::firstOrCreate([
		    'name' => 'admin'
		]);


        foreach ($allAdmins as $admin) {
        	
        	$allPermissions = Permission::all()->pluck('id');

			$role->permissions()->sync($allPermissions);
        	$admin->roles()->sync($role->id);

        }
    }
}
