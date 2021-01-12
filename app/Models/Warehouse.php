<?php

namespace App\Models;

use Illuminate\Support\Facades\File;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Warehouse extends Authenticatable
{
    use SoftDeletes, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'code', 'user_name', 'email', 'mobile', 'password', 'site_map_preview', 'lat', 'lng', 'warehouse_deal', 'active', 'warehouse_owner_id'
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
     * Set the user's first name.
     *
     * @param  string  $value
     * @return void
     */
    public function setMapPreviewAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {
            
            $imagePath = 'uploads/warehouse/';

            if(!File::isDirectory($imagePath)){
                File::makeDirectory($imagePath, 0777, true, true);
            }

            $img = Image::make($encodedImageFile);
            $img->save($imagePath.$this->id.'-site-map.jpg');

            $this->attributes['site_map_preview'] = $imagePath.$this->id.'-site-map.jpg';

        }
    }

    /**
     * Set the user's first name.
     *
     * @param  string  $value
     * @return void
     */
    public function setWarehousePreviewsAttribute($encodedPreviews = array())
    {
        if (count($encodedPreviews)) {
            
            $imagePath = 'uploads/warehouse/';

            if(!File::isDirectory($imagePath)){
                File::makeDirectory($imagePath, 0777, true, true);
            }

            // storing previews to the directory
            foreach ($encodedPreviews as $key => $warehousePreview) {

                $img = Image::make($warehousePreview['preview']);
                $img->save($imagePath.$this->id.'-preview-'.($key+1).'.jpg');

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
                        'preview' => $imagePath.$this->id.'-preview-'.($i+1).'.jpg',
                    ]);

                }

            }

        }
    }

    public function setWarehouseStoragesAttribute($storages = array())
    {
        if (count($storages)) {
            
            $storages = json_decode(json_encode($storages));

            $imagePath = 'uploads/warehouse/';

            if(!File::isDirectory($imagePath)){
                File::makeDirectory($imagePath, 0777, true, true);
            }

            // deleting warehouse all storage-types initially
            $this->storages()->delete();

            foreach ($storages as $key => $storage) {

                // if given storage type was already deleted
                if (empty($storage->deleted_at)) {
                    
                    // storing previews to the directory
                    foreach ($storage->previews as $index => $storagePreview) {

                        $img = Image::make($storagePreview->preview);
                        $img->save($imagePath.$this->id.'-'.$storage->storage_type->id.'-preview-'.($index+1).'.jpg');

                    }

                    $warehouseStorageType = $this->storages()->updateOrCreate(
                        ['storage_type_id' => $storage->storage_type->id, 'warehouse_id' => $this->id],
                        ['deleted_at' => NULL]
                    );

                    // if existing preview number of this warehouse-storage is greater than updated preview number 
                    if ($warehouseStorageType->previews->count() > count($storage->previews)) {
                        
                        // removing extra previews from directory & database
                        for ($i = count($storage->previews); $i < $warehouseStorageType->previews->count(); $i++) { 
                            
                            $storageLastPreview = $warehouseStorageType->previews()->orderBy('id', 'desc')->first();
                            File::delete($storageLastPreview->preview);         // deleting preview from directory
                            $storageLastPreview->delete();                     // deleting preview from database

                        }

                    }else {

                        // adding previews to the database
                        for ($i = $warehouseStorageType->previews->count(); $i < count($storage->previews); $i++) { 
                            
                            // adding previews to the database
                            $warehouseStorageType->previews()->create([
                                'preview' => $imagePath.$this->id.'-'.$storage->storage_type->id.'-preview-'.($i+1).'.jpg', 
                            ]);

                        }

                    }

                    $warehouseStorageType->feature()->updateOrCreate(
                        [ 'warehouse_storage_type_id'=> $warehouseStorageType->id ],
                        [ 'features' => $storage->feature->features, ]
                    );

                }

            }

            // as we initially deleted all related storage types, now deleting previews and feature of related storage type
            foreach ($this->storages()->onlyTrashed()->get() as $key => $warehouseDeletedStorageType) {
                
                // deleting preview from directory
                foreach ($warehouseDeletedStorageType->previews as $warehouseDeletedStoragePreview) {
                    File::delete($warehouseDeletedStoragePreview->preview);
                }

                // deleting from database
                $warehouseDeletedStorageType->previews()->delete();
                $warehouseDeletedStorageType->feature()->delete();
                $warehouseDeletedStorageType->forceDelete();

            }

        }
    }

    public function setWarehouseContainersAttribute($inputedContainers = array())
    {
        if (count($inputedContainers)) {
            
            // deleting all containers of this warehouse initially
            $this->containers()->delete();

            $inputedContainers = json_decode(json_encode($inputedContainers));
            
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
                        

                        $allRemoved = $this->removeExtraContainerProperties($warehouseExistingContainer, $inputedContainer);


                        if ($inputedContainer->quantity > 0) {
                            
                            if ($allRemoved) {
                                // update container quantity to warehouse containers
                                $this->updateContainerQuantityStatus($warehouseExistingContainer, $inputedContainer);
                            }

                            // updating old rents
                            $this->createContainerUpdatedRents($warehouseExistingContainer, $inputedContainer);
                        
                        }
                        else if ($allRemoved && $inputedContainer->quantity==0) {
                               
                            $warehouseExistingContainer->unit()->rents()->delete();
                            $warehouseExistingContainer->shelf()->rents()->delete();
                            $warehouseExistingContainer->rents()->delete();

                            $warehouseExistingContainer->unit()->delete();
                            $warehouseExistingContainer->shelf()->delete();
                            $warehouseExistingContainer->forceDelete();

                        }

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

    /**
     * Get the user's image.
     */
    public function previews()
    {
        return $this->hasMany(WarehousePreview::class, 'warehouse_id');
    }

    public function feature()
    {
        return $this->hasOne(WarehouseFeature::class, 'warehouse_id');
    }

    public function storages()
    {
        return $this->hasMany(WarehouseStorageType::class, 'warehouse_id')->withTrashed();
    }

    public function owner()
    {
        return $this->belongsTo(WarehouseOwner::class, 'warehouse_owner_id');
    }

    /**
     * Get all of the posts for the country.
     */
    public function storagePreviews()
    {
        return $this->hasManyThrough(WarehouseStoragePreview::class, WarehouseStorageType::class);
    }

    public function storageFeatures()
    {
        return $this->hasManyThrough(WarehouseStorageFeature::class, WarehouseStorageType::class);
    }

/*
    public function containerTypes()
    {
        return $this->hasMany(WarehouseContainerType::class, 'warehouse_id')->withTrashed();
    }
*/

    public function containers()
    {
        return $this->hasMany(WarehouseContainer::class, 'warehouse_id')->withTrashed();
    }

    protected function createContainerUpdatedRents($warehouseContainer, $inputedContainer)
    {          
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
            if (!empty($inputedContainer->rents->{'container_storing_price_'.$rentPeriod->name}) && !empty($inputedContainer->rents->{'container_selling_price_'.$rentPeriod->name})) {
                
                $warehouseContainer->rents()->create([
                    'storing_price' => $inputedContainer->rents->{'container_storing_price_'.$rentPeriod->name},
                    'selling_price' => $inputedContainer->rents->{'container_selling_price_'.$rentPeriod->name},
                    'rent_period_id' => $rentPeriod->id,
                ]);

            }

            // shelf rents
            if ($inputedContainer->container->has_shelve && !empty($inputedContainer->rents->{'shelf_storing_price_'.$rentPeriod->name}) && !empty($inputedContainer->rents->{'shelf_selling_price_'.$rentPeriod->name})) {
                
                $warehouseContainer->shelf->rents()->create([
                    'storing_price' => $inputedContainer->rents->{'shelf_storing_price_'.$rentPeriod->name},
                    'selling_price' => $inputedContainer->rents->{'shelf_selling_price_'.$rentPeriod->name},
                    'rent_period_id' => $rentPeriod->id,
                ]);

                // unit rents
                if ($inputedContainer->container->shelf->has_units && !empty($inputedContainer->rents->{'unit_storing_price_'.$rentPeriod->name}) && !empty($inputedContainer->rents->{'unit_selling_price_'.$rentPeriod->name})) {
                    
                    $warehouseContainer->shelf->unit->rents()->create([
                        'storing_price' => $inputedContainer->rents->{'unit_storing_price_'.$rentPeriod->name},
                        'selling_price' => $inputedContainer->rents->{'unit_selling_price_'.$rentPeriod->name},
                        'rent_period_id' => $rentPeriod->id,
                    ]);

                }
                
            }


        }

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

            // container statuses
            $warehouseContainerStatus = $warehouseContainer->containerStatuses()->create([
                'name' => 'cnt'.$inputedContainer->container->id.'-'.($i+1),
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
                'name' => 'cnt'.$inputedContainer->container->id.'-'.($i+1),
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
        // descending
        for($i=$warehouseContainer->quantity; $i>$inputedContainer->quantity; $i--) {

            // warehouse last container of required type
            $warehouseLastContainer = $warehouseContainer->containerStatuses()->latest('id')->first();

            // if completely vacant when 1 is full enaged and .5 is partially enganged
            if ($warehouseLastContainer->engaged===0) {
                
                // deleting related units and shelves
                $warehouseLastContainer->containerShelfUnitStatuses()->delete();
                $warehouseLastContainer->containerShelfStatuses()->delete();
                $warehouseLastContainer->delete();

            }
            else {
                break;
            }

        }

        // if loop was completed
        if ($i===$inputedContainer->quantity) {
           return true; 
        }

        return false;
    }

    public function removeDeletedContainers()
    {
        foreach ($this->containers()->onlyTrashed()->get() as $warehouseTrashedContainer) {

            // warehouse last container of required type
            $warehouseEngagedContainer = $warehouseTrashedContainer->containerStatuses()->where('engaged', '!=', 0)->exists();

            // if completely vacant when 1 is full enaged and .5 is partially enganged
            if ($warehouseEngagedContainer) {
                
                $warehouseTrashedContainer->update([
                    'deleted_at' => NULL
                ]);

            }
            else { // deleting related units and shelves
                
                if ($warehouseTrashedContainer->container->has_shelve) {

                    if ($warehouseTrashedContainer->container->shelf->has_units) {
                        
                        $warehouseTrashedContainer->unit->rents()->delete();
                        $warehouseTrashedContainer->containerShelfUnitStatuses()->delete();
                        $warehouseTrashedContainer->shelf->unit->delete();

                    }
                    
                    $warehouseTrashedContainer->shelf->rents()->delete();
                    $warehouseTrashedContainer->containerShelfStatuses()->delete();
                    $warehouseTrashedContainer->shelf->delete();

                }

                $warehouseTrashedContainer->rents()->delete();
                $warehouseTrashedContainer->containerStatuses()->delete();
                $warehouseTrashedContainer->forceDelete();

            }

        }
    }
}
