<?php

namespace App\Models;

use Illuminate\Support\Arr;
use App\Traits\HasPermissionsTrait;
use Illuminate\Support\Facades\File;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Intervention\Image\ImageManagerStatic as Image;
use Intervention\Image\Exception\NotReadableException;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Warehouse extends Authenticatable
{
    use SoftDeletes, Notifiable, HasPermissionsTrait;

    protected $guard = 'warehouse';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'user_name', 'email', 'mobile', 'password', 'site_map_preview', 'lat', 'lng', 'warehouse_deal', 'active', 'warehouse_owner_id'
    ];
    
   
    // protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    // protected $with = [];

    /**
     * Get the warehouse's previews.
     */
    public function previews()
    {
        return $this->hasMany(WarehousePreview::class, 'warehouse_id');
    }

    public function owner()
    {
        return $this->belongsTo(WarehouseOwner::class, 'warehouse_owner_id')->withTrashed();
    }

    public function feature()
    {
        return $this->hasOne(WarehouseFeature::class, 'warehouse_id');
    }

    public function storages()
    {
        return $this->hasMany(WarehouseStorageType::class, 'warehouse_id')->withTrashed();
    }

    /**
     * Get all of the posts for the country.
     */
    
/*
    public function storagePreviews()
    {
        return $this->hasManyThrough(WarehouseStoragePreview::class, WarehouseStorageType::class);
    }

    public function storageFeatures()
    {
        return $this->hasManyThrough(WarehouseStorageFeature::class, WarehouseStorageType::class);
    }

    public function containerTypes()
    {
        return $this->hasMany(WarehouseContainerType::class, 'warehouse_id')->withTrashed();
    }
*/

    // to update warehouse-containers easily
    public function containers()
    {
        return $this->hasMany(WarehouseContainer::class, 'warehouse_id')->withTrashed();
    }

    /*
    public function deals()
    {
        return $this->hasMany(DealtSpace::class, 'warehouse_id');
    }
    */

    public function containerStatuses()
    {
        return $this->hasManyThrough(WarehouseContainerStatus::class, WarehouseContainer::class, 'warehouse_id', 'warehouse_container_id');
    }

    public function containerShelfStatuses()
    {
        return $this->hasManyThrough(WarehouseContainerShelfStatus::class, WarehouseContainer::class, 'warehouse_id', 'warehouse_container_id');
    }

    public function containerShelfUnitStatuses()
    {
        return $this->hasManyThrough(WarehouseContainerShelfUnitStatus::class, WarehouseContainer::class, 'warehouse_id', 'warehouse_container_id');
    }

    /**
     * The managers that belong to the warehouse.
     */
    public function managers()
    {
        return $this->belongsToMany(Manager::class, 'warehouse_managers', 'warehouse_id', 'manager_id');
    }

    /**
     * Set the user's first name.
     *
     * @param  string  $value
     * @return void
     */
    public function setMapPreviewAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {
            
            $imagePath = 'storage/warehouses/';

            if(!File::isDirectory($imagePath)){
                File::makeDirectory($imagePath, 0777, true, true);
            }

            try 
            {
                $img = Image::make($encodedImageFile);
            }
            catch(NotReadableException $e)
            {
                // If error, stop and return
                return;
            }

            $img->save($imagePath.'w-'.$this->id.'-site-map.jpg', 100);

            $this->attributes['site_map_preview'] = $imagePath.'w-'.$this->id.'-site-map.jpg';

        }
    }

    /**
     * Set the warehouse's preview.
     *
     * @param  string  $value
     * @return void
     */
    public function setWarehousePreviewsAttribute($encodedPreviews = array())
    {
        if (count($encodedPreviews)) {
            
            $imagePath = 'storage/warehouses/';

            if(!File::isDirectory($imagePath)){
                File::makeDirectory($imagePath, 0777, true, true);
            }

            // storing previews to the directory
            foreach ($encodedPreviews as $i => $warehousePreview) {

                try 
                {
                    $img = Image::make($warehousePreview->preview);
                }
                catch(NotReadableException $e)
                {
                    // If error, stop and return
                    continue;
                }

                $img->save($imagePath.'w-'.$this->id.'-preview-'.($i+1).'.jpg', 100);

            }

            // if existing preview number is greater than updated preview number 
            if ($this->previews->count() > count($encodedPreviews)) {
                
                // removing extra previews from directory & database
                for ($i = count($encodedPreviews); $i < $this->previews->count(); $i++) { 
                    
                    $warehouseLastPreview = $this->previews()->orderBy('id', 'desc')->first();
                    File::delete($warehouseLastPreview->preview);         // deleting preview from directory
                    $warehouseLastPreview->delete();                     // deleting preview from database

                }

            }else {

                // adding previews to the database
                for ($i = $this->previews->count(); $i < count($encodedPreviews); $i++) { 
                    
                    $this->previews()->create([
                        'preview' => $imagePath.'w-'.$this->id.'-preview-'.($i+1).'.jpg',
                    ]);

                }

            }

        }
    }

    public function setWarehouseStoragesAttribute($storages = array())
    {
        if (count($storages)) {
            
            // $storages = json_decode(json_encode($storages));

            $imagePath = 'storage/warehouses/';

            if(!File::isDirectory($imagePath)){
                File::makeDirectory($imagePath, 0777, true, true);
            }

            // deleting warehouse all storage-types initially
            $this->storages()->delete();

            // creating new storages
            foreach ($storages as $newStorageIndex => $newStorage) {
                
                $warehouseNewStorageType = $this->storages()->updateOrCreate(
                    ['storage_type_id' => $newStorage->storage_type->id, 'warehouse_id' => $this->id],
                    ['deleted_at' => NULL]
                );

                $warehouseStorageTypeNewFeature = $warehouseNewStorageType->feature()->updateOrCreate(
                    ['warehouse_storage_type_id' => $warehouseNewStorageType->id],
                    ['features' => $newStorage->feature->features]
                );

                // storing previews to the directory & database
                foreach ($newStorage->previews as $newStoragePreviewIndex => $newStoragePreview) {

                    try 
                    {
                        $img = Image::make($newStoragePreview->preview);
                    }
                    catch(NotReadableException $e)
                    {
                        // If error, stop and return
                        continue;
                    }

                    $img->save($imagePath.'w-'.$this->id.'-st-'.$newStorage->storage_type->id.'-preview-'.($newStoragePreviewIndex+1).'.jpg', 100);

                    $warehouseStorageNewPreview = $warehouseNewStorageType->previews()->firstOrCreate([
                        'preview' => $imagePath.'w-'.$this->id.'-st-'.$newStorage->storage_type->id.'-preview-'.($newStoragePreviewIndex+1).'.jpg'
                    ]);

                }

                if (count($newStorage->previews) < $warehouseNewStorageType->previews->count()) {
                    
                    for ($i=count($newStorage->previews); $i<$warehouseNewStorageType->previews->count(); $i++) { 
                        
                        // deleting preview from directory and database
                        File::delete($warehouseNewStorageType->previews()->skip($i)->first()->preview);
                        $warehouseNewStorageType->previews()->skip($i)->first()->delete();

                    }

                }

            }

              // remove trashed warehouse-storages 
            foreach ($this->storages()->onlyTrashed()->get() as $trashedStorageIndex => $warehouseTrashedStorageType) {
                
                foreach ($warehouseTrashedStorageType->previews as $storagePreviewIndex => $warehouseStoragePreview) {
                    
                    File::delete($warehouseStoragePreview->preview);
                    $warehouseStoragePreview->delete();

                }

                $warehouseTrashedStorageType->feature()->delete();
                $warehouseTrashedStorageType->forcedelete();

            }

        }
    }

    public function setWarehouseContainersAttribute($inputedContainers = array())
    {
        if (count($inputedContainers)) {
            
            // deleting all containers of this warehouse initially
            $this->containers()->delete();

            // $inputedContainers = json_decode(json_encode($inputedContainers));
            
            foreach ($inputedContainers as $inputedContainer) {
                
                $warehouseExistingContainer = $this->containers()->where('container_id', $inputedContainer->container->id)->first();

                if ($warehouseExistingContainer) {
                    // if quantity is equal to previous quantity
                    if ($warehouseExistingContainer->quantity == $inputedContainer->quantity) {

                        // update container quantity to warehouse containers
                        $this->updateContainerQuantityStatus($warehouseExistingContainer, $inputedContainer);

                        // updating old rents
                        $this->createContainerUpdatedRents($warehouseExistingContainer, $inputedContainer);

                    }
                    // if inputed container is already in enlisted containers for this warehouse and previous quantity is less than updated quantity
                    else if ($warehouseExistingContainer->quantity < $inputedContainer->quantity) {

                        // add new containers and properties to warehouse
                        $this->addNewContainerProperties($warehouseExistingContainer, $inputedContainer);

                        // update container quantity to warehouse containers
                        $this->updateContainerQuantityStatus($warehouseExistingContainer, $inputedContainer);

                        // updating old rents
                        $this->createContainerUpdatedRents($warehouseExistingContainer, $inputedContainer);

                    }
                    // if inputed container is already in enlisted containers for this warehouse and quantity is reduced
                    else if ($warehouseExistingContainer->quantity > $inputedContainer->quantity) {
                        

                        $removedContainerNumber = $this->removeExtraContainerProperties($warehouseExistingContainer, $inputedContainer);
                            
                        if ($removedContainerNumber != ($warehouseExistingContainer->quantity - $inputedContainer->quantity)) {
                        
                            $inputedContainer->quantity = ($warehouseExistingContainer->quantity - $removedContainerNumber);
                        
                        }

                        // update container quantity to warehouse containers
                        $this->updateContainerQuantityStatus($warehouseExistingContainer, $inputedContainer);
                        // updating old rents
                        $this->createContainerUpdatedRents($warehouseExistingContainer, $inputedContainer);
                    
                    /*
                        if ($removedContainerNumber==$warehouseExistingContainer->quantity && $inputedContainer->quantity==0) {
                               
                            $warehouseExistingContainer->unit()->rents()->delete();
                            $warehouseExistingContainer->shelf()->rents()->delete();
                            $warehouseExistingContainer->rents()->delete();

                            $warehouseExistingContainer->unit()->delete();
                            $warehouseExistingContainer->shelf()->delete();
                            $warehouseExistingContainer->forceDelete();

                        }
                    */

                    }   

                }

                // else inputed container is new for this warehouse
                else if(is_null($warehouseExistingContainer)) {
                    
                    // add currently working container to warehouse
                    $warehouseContainer = $this->createNewContainerToWarehouse($inputedContainer);

                    // add new containers and properties to warehouse
                    $this->createNewContainerProperties($warehouseContainer, $inputedContainer);

                    $this->createContainerUpdatedRents($warehouseContainer, $inputedContainer);

                }

            }

            // the deleted containers
            $this->removeDeletedContainers();

        }

    }

    /*
    public function setUserPermissionsAttribute($permissions = [])
    {
        // if (count($permissions)) {
            
            $this->permissions()->detach();
            $this->permissions()->attach(Arr::pluck($permissions, 'id'));

        // }
    }

    public function setUserRolesAttribute($roles = [])
    {
        // if (count($roles)) {
            
            $this->roles()->detach();
            $this->roles()->attach(Arr::pluck($roles, 'id'));

        // }
    }
    */

/*
    // new
    public function setWarehouseContainersAttribute($inputedContainers=array())
    {
        if ($inputedContainers) {
            
            if ($this->con) {
                # code...
            }

        }
    }
*/

/*
    public function setManagersAttribute($managers = [])
    {
        if (count($managers)) {
            
            $this->managers()->detach();
            $this->managers()->attach(Arr::pluck($managers, 'id'));

        }
    }
*/

    protected function createContainerUpdatedRents($warehouseContainer, $inputedContainer)
    {          
        // primary deletion
        if ($inputedContainer->container->has_shelve) {
            
            if ($inputedContainer->container->shelf->has_units) {
                
                $warehouseContainer->unit->rents()->delete();
            
            }

            $warehouseContainer->shelf->rents()->delete();
        }

        $warehouseContainer->rents()->delete();

        // creating new rents
        $allRentPeriods = RentPeriod::all();

        foreach ($allRentPeriods as $rentPeriod) {

            // container rents 
            if (! empty($inputedContainer->rents->{'container_rent_'.$rentPeriod->name}->rent) && $inputedContainer->rents->{'container_rent_'.$rentPeriod->name}->rent > 0 /* && !empty($inputedContainer->rents->{'container_selling_price_'.$rentPeriod->name}) */) {
                
                $warehouseContainer->rents()->updateOrCreate(
                    [ 'rent_period_id' => $rentPeriod->id ],
                    [
                        'rent' => $inputedContainer->rents->{'container_rent_'.$rentPeriod->name}->rent,
                        'deleted_at' => NULL
                        // 'selling_price' => $inputedContainer->rents->{'container_selling_price_'.$rentPeriod->name},
                    ]
                );

            }

            // shelf rents
            if ($inputedContainer->container->has_shelve && ! empty($inputedContainer->rents->{'shelf_rent_'.$rentPeriod->name}->rent) && $inputedContainer->rents->{'shelf_rent_'.$rentPeriod->name}->rent > 0 /* && !empty($inputedContainer->rents->{'shelf_selling_price_'.$rentPeriod->name}) */) {
                
                $warehouseContainer->shelf->rents()->updateOrCreate(
                    [ 'rent_period_id' => $rentPeriod->id ],
                    [
                        'rent' => $inputedContainer->rents->{'shelf_rent_'.$rentPeriod->name}->rent,
                        'deleted_at' => NULL
                        // 'selling_price' => $inputedContainer->rents->{'shelf_selling_price_'.$rentPeriod->name},
                    ]
                );

                // unit rents
                if ($inputedContainer->container->shelf->has_units && ! empty($inputedContainer->rents->{'unit_rent_'.$rentPeriod->name}->rent) && $inputedContainer->rents->{'unit_rent_'.$rentPeriod->name}->rent > 0 /* && !empty($inputedContainer->rents->{'unit_selling_price_'.$rentPeriod->name}) */) {
                    
                    $warehouseContainer->shelf->unit->rents()->updateOrCreate(
                        [  'rent_period_id' => $rentPeriod->id ],
                        [
                            'rent' => $inputedContainer->rents->{'unit_rent_'.$rentPeriod->name}->rent,
                            'deleted_at' => NULL
                            // 'selling_price' => $inputedContainer->rents->{'unit_selling_price_'.$rentPeriod->name},
                        ]
                    );

                }
                
            }

        }

        $this->deleteTrashedRents($warehouseContainer);

    }

    protected function deleteTrashedRents($warehouseContainer)
    {
        if ($warehouseContainer->shelf) {
            
            if ($warehouseContainer->shelf->unit) {
                
                $warehouseContainer->shelf->unit->rents()->onlyTrashed()->forcedelete();
            
            }

            $warehouseContainer->shelf->rents()->onlyTrashed()->forcedelete();
        }

        $warehouseContainer->rents()->onlyTrashed()->forcedelete();
    }

    protected function updateContainerQuantityStatus($warehouseContainer, $inputedContainer)
    {
        $warehouseContainer->update([
            'quantity' => $inputedContainer->quantity,
            'deleted_at' => NULL,
        ]);
    }

    protected function createNewContainerToWarehouse($inputedContainer)
    {
        $warehouseContainer = $this->containers()->create([
            'quantity' => $inputedContainer->quantity,
            'container_id' => $inputedContainer->container->id,
        ]);

        // $currentContainer = Container::find($inputedContainer->container->id);

        if ($inputedContainer->container->has_shelve) {
            
            $warehouseContainerShelf = $warehouseContainer->shelf()->create([]);

            if ($inputedContainer->container->shelf->has_units) {
                
                $warehouseContainerShelfUnit = $warehouseContainerShelf->unit()->create([]);

            }

        }

        return $warehouseContainer;
    }

    // increase quantity of required container
    protected function createNewContainerProperties($warehouseContainer, $inputedContainer)
    {
        for($i=0; $i<$inputedContainer->quantity; $i++) {

            // $containerToAdd = Container::findOrFail($inputedContainer->container->id);

            // container statuses
            $warehouseContainerStatus = $warehouseContainer->containerStatuses()->create([
                'name' => (($inputedContainer->container->code ?? 'cnt'.$inputedContainer->container->id).'-'.($i+1)),
            ]);

            if ($inputedContainer->container->has_shelve) {
                
                // shelves per container
                for($j=0; $j<$inputedContainer->container->shelf->quantity; $j++) {

                    $warehouseContainerShelfStatus = $warehouseContainerStatus->containerShelfStatuses()->create([
                        'name' => $warehouseContainerStatus->name.'-shl-'.($j+1),
                        'warehouse_container_id' => $warehouseContainer->id,
                    ]);

                    if ($inputedContainer->container->shelf->has_units) {
                        
                        // units per shelf
                        for($k=0; $k<$inputedContainer->container->shelf->unit->quantity; $k++){

                            $warehouseContainerShelfStatus->containerShelfUnitStatuses()->create([
                                'name' => $warehouseContainerShelfStatus->name.'-unt-'.($k+1),
                                'warehouse_container_id' => $warehouseContainer->id,
                            ]);    

                        }

                    }

                }

            }

        }
    }

    // increase quantity of required container
    protected function addNewContainerProperties($warehouseContainer, $inputedContainer)
    {
        for($i=$warehouseContainer->quantity; $i<$inputedContainer->quantity; $i++) {

            // container statuses
            $warehouseContainerStatus = $warehouseContainer->containerStatuses()->create([
                'name' => (($inputedContainer->container->code ?? 'cnt'.$inputedContainer->container->id).'-'.($i+1)),
            ]);

            if ($inputedContainer->container->has_shelve) {
                
                // shelves per container
                for($j=0; $j<$inputedContainer->container->shelf->quantity; $j++) {

                    $warehouseContainerShelfStatus = $warehouseContainerStatus->containerShelfStatuses()->create([
                        'name' => $warehouseContainerStatus->name.'-shl-'.($j+1),
                        'warehouse_container_id' => $warehouseContainer->id,
                    ]);

                    if ($inputedContainer->container->shelf->has_units) {
                        
                        // units per shelf
                        for($k=0; $k<$inputedContainer->container->shelf->unit->quantity; $k++){

                            $warehouseContainerShelfStatus->containerShelfUnitStatuses()->create([
                                'name' => $warehouseContainerShelfStatus->name.'-unt-'.($k+1),
                                'warehouse_container_id' => $warehouseContainer->id,
                            ]);    

                        }
                        
                    }

                }

            }

        }
    }

    protected function removeExtraContainerProperties($warehouseContainer, $inputedContainer)
    {
        $deletedContainers = 0;

        // descending
        for($i=$inputedContainer->quantity; $i<$warehouseContainer->quantity; $i++) {

            // warehouse last container of required type
            $warehouseLastContainer = $warehouseContainer->containerStatuses()->where('engaged', 0.0)->latest('id')->first();

            // if completely vacant when 1 is full enaged and .5 is partially enganged
            if ($warehouseLastContainer->engaged==0.0) {
                
                $deletedContainers++;

                // deleting related units and shelves
                $warehouseLastContainer->containerShelfUnitStatuses()->delete();
                $warehouseLastContainer->containerShelfStatuses()->delete();
                $warehouseLastContainer->delete();

            }

        }

        return $deletedContainers;
    }

    protected function removeDeletedContainers()
    {
        foreach ($this->containers()->onlyTrashed()->get() as $warehouseTrashedContainer) {

            // warehouse last container of required type
            $warehouseEngagedContainer = $warehouseTrashedContainer->containerStatuses()->where('engaged', '>', 0)->exists();

            // if completely vacant when 1 is full enaged and .5 is partially enganged
            if ($warehouseEngagedContainer) {
                
                $warehouseTrashedContainer->update([
                    'deleted_at' => NULL
                ]);

            }
            else { // deleting related units and shelves
                
                if ($warehouseTrashedContainer->container->has_shelve) {

                    if ($warehouseTrashedContainer->container->shelf->has_units) {
                        
                        $warehouseTrashedContainer->unit->rents()->forcedelete();
                        $warehouseTrashedContainer->containerShelfUnitStatuses()->delete();
                        $warehouseTrashedContainer->shelf->unit->delete();

                    }
                    
                    $warehouseTrashedContainer->shelf->rents()->forcedelete();
                    $warehouseTrashedContainer->containerShelfStatuses()->delete();
                    $warehouseTrashedContainer->shelf->delete();

                }

                $warehouseTrashedContainer->rents()->forcedelete();
                $warehouseTrashedContainer->containerStatuses()->delete();
                $warehouseTrashedContainer->forceDelete();

            }

        }
    }

    /*
    protected function inputedContainerHasValidRent($inputedContainerRent)
    {
        foreach ($inputedContainerRent as $key => $value) {
            
            if ($value > 0) {
                return true;
            }

        }

        return false;
    }
    */

}
