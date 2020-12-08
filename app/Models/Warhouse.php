<?php

namespace App\Models;

use App\Models\WarhouseOwner;
use App\Models\WarhousePreview;
use App\Models\WarhouseFeature;
use App\Models\WarhouseContainer;
use App\Models\WarhouseStorageType;
use Illuminate\Support\Facades\File;
use App\Models\WarhouseStoragePreview;
use App\Models\WarhouseStorageFeature;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Warhouse extends Authenticatable
{
    use SoftDeletes, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    /*
        protected $fillable = [
            'name', 'code', 'user_name', 'email', 'mobile', 'password',
        ];
    */
   
    protected $guarded = ['id'];

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
            
            $imagePath = 'uploads/warhouse/';

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
    public function setWarhousePreviewsAttribute($encodedPreviews = array())
    {
        if (count($encodedPreviews)) {
            
            $imagePath = 'uploads/warhouse/';

            if(!File::isDirectory($imagePath)){
                File::makeDirectory($imagePath, 0777, true, true);
            }

            // storing previews to the directory
            foreach ($encodedPreviews as $key => $warhousePreview) {

                $img = Image::make($warhousePreview['preview']);
                $img->save($imagePath.$this->id.'-preview-'.($key+1).'.jpg');

            }

            // if existing preview number is greater than updated preview number 
            if ($this->previews->count() > count($encodedPreviews)) {
                
                // removing extra previews from directory & database
                for ($i = count($encodedPreviews); $i < $this->previews->count(); $i++) { 
                    
                    $warhouseLastPreview = $this->previews()->orderBy('id', 'desc')->first();
                    File::delete($warhouseLastPreview->preview);         // deleting preview from directory
                    $warhouseLastPreview->delete();                     // deleting preview from database

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

    public function setWarhouseStoragesAttribute($storages = array())
    {
        if (count($storages)) {
            
            $storages = json_decode(json_encode($storages));

            $imagePath = 'uploads/warhouse/';

            if(!File::isDirectory($imagePath)){
                File::makeDirectory($imagePath, 0777, true, true);
            }

            // deleting warhouse all storage-types initially
            $this->storages()->delete();

            foreach ($storages as $key => $storage) {

                // if given storage type was already deleted
                if (empty($storage->deleted_at)) {
                    
                    // storing previews to the directory
                    foreach ($storage->previews as $index => $storagePreview) {

                        $img = Image::make($storagePreview->preview);
                        $img->save($imagePath.$this->id.'-'.$storage->storage_type->id.'-preview-'.($index+1).'.jpg');

                    }

                    $warhouseStorageType = $this->storages()->updateOrCreate(
                        ['storage_type_id' => $storage->storage_type->id, 'warhouse_id' => $this->id],
                        ['deleted_at' => NULL]
                    );

                    // if existing preview number of this warhouse-storage is greater than updated preview number 
                    if ($warhouseStorageType->previews->count() > count($storage->previews)) {
                        
                        // removing extra previews from directory & database
                        for ($i = count($storage->previews); $i < $warhouseStorageType->previews->count(); $i++) { 
                            
                            $storageLastPreview = $warhouseStorageType->previews()->orderBy('id', 'desc')->first();
                            File::delete($storageLastPreview->preview);         // deleting preview from directory
                            $storageLastPreview->delete();                     // deleting preview from database

                        }

                    }else {

                        // adding previews to the database
                        for ($i = $warhouseStorageType->previews->count(); $i < count($storage->previews); $i++) { 
                            
                            // adding previews to the database
                            $warhouseStorageType->previews()->create([
                                'preview' => $imagePath.$this->id.'-'.$storage->storage_type->id.'-preview-'.($i+1).'.jpg', 
                            ]);

                        }

                    }

                    $warhouseStorageType->feature()->updateOrCreate(
                        [ 'warhouse_storage_type_id'=> $warhouseStorageType->id ],
                        [ 'features' => $storage->feature->features, ]
                    );

                }

            }

            // as we initially deleted all related storage types, now deleting previews and feature of related storage type
            foreach ($this->storages()->onlyTrashed()->get() as $key => $warhouseDeletedStorageType) {
                
                // deleting preview from directory
                foreach ($warhouseDeletedStorageType->previews as $warhouseDeletedStoragePreview) {
                    File::delete($warhouseDeletedStoragePreview->preview);
                }

                // deleting from database
                $warhouseDeletedStorageType->previews()->delete();
                $warhouseDeletedStorageType->feature()->delete();
                $warhouseDeletedStorageType->forceDelete();

            }

        }
    }

    public function setWarhouseContainersAttribute($inputedContainers = array())
    {
        if (count($inputedContainers)) {
            
            // deleting all containers of this warhouse initially
            $this->containers()->delete();

            $inputedContainers = json_decode(json_encode($inputedContainers));
            
            foreach ($inputedContainers as $inputedContainer) {
                
                $warhouseExistingContainer = $this->containers()->where('container_id', $inputedContainer->container->id)->first();

                if ($warhouseExistingContainer) {
                    // if quantity is equal to previous quantity
                    if ($warhouseExistingContainer->quantity == $inputedContainer->quantity) {

                        // update container quantity to warhouse containers
                        $this->updateContainerQuantityStatus($warhouseExistingContainer, $inputedContainer);

                        // updating old rents
                        $this->createContainerUpdatedRents($warhouseExistingContainer, $inputedContainer);

                    }
                    // if inputed container is already in enlisted containers for this warehouse and previous quantity is less than updated quantity
                    else if ($warhouseExistingContainer->quantity < $inputedContainer->quantity) {

                        // add new containers and properties to warhouse
                        $this->addNewContainerProperties($warhouseExistingContainer, $inputedContainer);

                        // update container quantity to warhouse containers
                        $this->updateContainerQuantityStatus($warhouseExistingContainer, $inputedContainer);

                        // updating old rents
                        $this->createContainerUpdatedRents($warhouseExistingContainer, $inputedContainer);

                    }
                    // if inputed container is already in enlisted containers for this warehouse and quantity is reduced
                    else if ($warhouseExistingContainer->quantity > $inputedContainer->quantity) {
                        

                        $allRemoved = $this->removeExtraContainerProperties($warhouseExistingContainer, $inputedContainer);


                        if ($inputedContainer->quantity > 0) {
                            
                            if ($allRemoved) {
                                // update container quantity to warhouse containers
                                $this->updateContainerQuantityStatus($warhouseExistingContainer, $inputedContainer);
                            }

                            // updating old rents
                            $this->createContainerUpdatedRents($warhouseExistingContainer, $inputedContainer);
                        
                        }
                        else if ($allRemoved && $inputedContainer->quantity==0) {
                               
                            $warhouseExistingContainer->unit()->rents()->delete();
                            $warhouseExistingContainer->shelf()->rents()->delete();
                            $warhouseExistingContainer->rents()->delete();

                            $warhouseExistingContainer->unit()->delete();
                            $warhouseExistingContainer->shelf()->delete();
                            $warhouseExistingContainer->forceDelete();

                        }

                    }   

                }

                // else inputed container is new for this warehouse
                else if(is_null($warhouseExistingContainer)) {
                    
                    // add currently working container to warhouse
                    $warhouseContainer = $this->createNewContainerToWarhouse($inputedContainer);

                    // add new containers and properties to warhouse
                    $this->createNewContainerProperties($warhouseContainer, $inputedContainer);

                    $this->createContainerUpdatedRents($warhouseContainer, $inputedContainer);

                }

            }

            // the deleted containers
            $this->removeDeletedContainers();

        }


    }

/*
    // new
    public function setWarhouseContainersAttribute($inputedContainers=array())
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
        return $this->hasMany(WarhousePreview::class, 'warhouse_id');
    }

    public function feature()
    {
        return $this->hasOne(WarhouseFeature::class, 'warhouse_id');
    }

    public function storages()
    {
        return $this->hasMany(WarhouseStorageType::class, 'warhouse_id')->withTrashed();
    }

    public function owner()
    {
        return $this->belongsTo(WarhouseOwner::class, 'warhouse_owner_id');
    }

    /**
     * Get all of the posts for the country.
     */
    public function storagePreviews()
    {
        return $this->hasManyThrough(WarhouseStoragePreview::class, WarhouseStorageType::class);
    }

    public function storageFeatures()
    {
        return $this->hasManyThrough(WarhouseStorageFeature::class, WarhouseStorageType::class);
    }

/*
    public function containerTypes()
    {
        return $this->hasMany(WarhouseContainerType::class, 'warhouse_id')->withTrashed();
    }
*/

    public function containers()
    {
        return $this->hasMany(WarhouseContainer::class, 'warhouse_id')->withTrashed();
    }

    protected function createContainerUpdatedRents($warhouseContainer, $inputedContainer)
    {          
        if ($inputedContainer->container->has_shelve) {
            
            if ($inputedContainer->container->shelf->has_units) {
                
                $warhouseContainer->unit->rents()->delete();
            
            }

            $warhouseContainer->shelf->rents()->delete();
        }

        $warhouseContainer->rents()->delete();

        // creating new rents
        $allRentPeriods = RentPeriod::all();

        foreach ($allRentPeriods as $rentPeriod) {

            // container rents 
            if (!empty($inputedContainer->rents->{'container_storing_price_'.$rentPeriod->name}) && !empty($inputedContainer->rents->{'container_selling_price_'.$rentPeriod->name})) {
                
                $warhouseContainer->rents()->create([
                    'storing_price' => $inputedContainer->rents->{'container_storing_price_'.$rentPeriod->name},
                    'selling_price' => $inputedContainer->rents->{'container_selling_price_'.$rentPeriod->name},
                    'rent_period_id' => $rentPeriod->id,
                ]);

            }

            // shelf rents
            if ($inputedContainer->container->has_shelve && !empty($inputedContainer->rents->{'shelf_storing_price_'.$rentPeriod->name}) && !empty($inputedContainer->rents->{'shelf_selling_price_'.$rentPeriod->name})) {
                
                $warhouseContainer->shelf->rents()->create([
                    'storing_price' => $inputedContainer->rents->{'shelf_storing_price_'.$rentPeriod->name},
                    'selling_price' => $inputedContainer->rents->{'shelf_selling_price_'.$rentPeriod->name},
                    'rent_period_id' => $rentPeriod->id,
                ]);

                // unit rents
                if ($inputedContainer->container->shelf->has_units && !empty($inputedContainer->rents->{'unit_storing_price_'.$rentPeriod->name}) && !empty($inputedContainer->rents->{'unit_selling_price_'.$rentPeriod->name})) {
                    
                    $warhouseContainer->shelf->unit->rents()->create([
                        'storing_price' => $inputedContainer->rents->{'unit_storing_price_'.$rentPeriod->name},
                        'selling_price' => $inputedContainer->rents->{'unit_selling_price_'.$rentPeriod->name},
                        'rent_period_id' => $rentPeriod->id,
                    ]);

                }
                
            }


        }

    }

    protected function updateContainerQuantityStatus($warhouseContainer, $inputedContainer)
    {
        $warhouseContainer->update([
            'quantity' => $inputedContainer->quantity,
            'deleted_at' => NULL,
        ]);
    }

    protected function createNewContainerToWarhouse($inputedContainer)
    {
        $warhouseContainer = $this->containers()->create([
            'quantity' => $inputedContainer->quantity,
            'container_id' => $inputedContainer->container->id,
        ]);

        // $currentContainer = Container::find($inputedContainer->container->id);

        if ($inputedContainer->container->has_shelve) {
            
            $warhouseContainerShelf = $warhouseContainer->shelf()->create([]);

            if ($inputedContainer->container->shelf->has_units) {
                
                $warhouseContainerShelfUnit = $warhouseContainerShelf->unit()->create([]);

            }

        }

        return $warhouseContainer;
    }

    // increase quantity of required container
    protected function createNewContainerProperties($warhouseContainer, $inputedContainer)
    {
        for($i=0; $i<$inputedContainer->quantity; $i++) {

            // container statuses
            $warhouseContainerStatus = $warhouseContainer->containerStatuses()->create([
                'name' => 'wr-'.$this->id.'-cnt-'.$inputedContainer->container->id.'-'.($i+1),
            ]);

            if ($inputedContainer->container->has_shelve) {
                
                // shelves per container
                for($j=0; $j<$inputedContainer->container->shelf->quantity; $j++) {

                    $warhouseContainerShelfStatus = $warhouseContainerStatus->containerShelfStatuses()->create([
                        'name' => $warhouseContainerStatus->name.'-shl-'.($j+1),
                        'warhouse_container_id' => $warhouseContainer->id,
                    ]);

                    if ($inputedContainer->container->shelf->has_units) {
                        
                        // units per shelf
                        for($k=0; $k<$inputedContainer->container->shelf->unit->quantity; $k++){

                            $warhouseContainerShelfStatus->containerShelfUnitStatuses()->create([
                                'name' => $warhouseContainerShelfStatus->name.'-unt-'.($k+1),
                                'warhouse_container_id' => $warhouseContainer->id,
                            ]);    

                        }

                    }

                }

            }

        }
    }

    // increase quantity of required container
    protected function addNewContainerProperties($warhouseContainer, $inputedContainer)
    {
        for($i=$warhouseContainer->quantity; $i<$inputedContainer->quantity; $i++) {

            // container statuses
            $warhouseContainerStatus = $warhouseContainer->containerStatuses()->create([
                'name' => 'wr-'.$this->id.'-cnt-'.$inputedContainer->container->id.'-'.($i+1),
            ]);

            if ($inputedContainer->container->has_shelve) {
                
                // shelves per container
                for($j=0; $j<$inputedContainer->container->shelf->quantity; $j++) {

                    $warhouseContainerShelfStatus = $warhouseContainerStatus->containerShelfStatuses()->create([
                        'name' => $warhouseContainerStatus->name.'-shl-'.($j+1),
                        'warhouse_container_id' => $warhouseContainer->id,
                    ]);

                    if ($inputedContainer->container->shelf->has_units) {
                        
                        // units per shelf
                        for($k=0; $k<$inputedContainer->container->shelf->unit->quantity; $k++){

                            $warhouseContainerShelfStatus->containerShelfUnitStatuses()->create([
                                'name' => $warhouseContainerShelfStatus->name.'-unt-'.($k+1),
                                'warhouse_container_id' => $warhouseContainer->id,
                            ]);    

                        }
                        
                    }

                }

            }

        }
    }

    protected function removeExtraContainerProperties($warhouseContainer, $inputedContainer)
    {
        // descending
        for($i=$warhouseContainer->quantity; $i>$inputedContainer->quantity; $i--) {

            // warhouse last container of required type
            $warhouseLastContainer = $warhouseContainer->containerStatuses()->latest('id')->first();

            // if completely vacant when 1 is full enaged and .5 is partially enganged
            if ($warhouseLastContainer->engaged===0) {
                
                // deleting related units and shelves
                $warhouseLastContainer->containerShelfUnitStatuses()->delete();
                $warhouseLastContainer->containerShelfStatuses()->delete();
                $warhouseLastContainer->delete();

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
        foreach ($this->containers()->onlyTrashed()->get() as $warhouseTrashedContainer) {

            // warhouse last container of required type
            $warhouseEngagedContainer = $warhouseTrashedContainer->containerStatuses()->where('engaged', '!=', 0)->exists();

            // if completely vacant when 1 is full enaged and .5 is partially enganged
            if ($warhouseEngagedContainer) {
                
                $warhouseTrashedContainer->update([
                    'deleted_at' => NULL
                ]);

            }
            else { // deleting related units and shelves
                
                if ($warhouseTrashedContainer->container->has_shelve) {

                    if ($warhouseTrashedContainer->container->shelf->has_units) {
                        
                        $warhouseTrashedContainer->unit->rents()->delete();
                        $warhouseTrashedContainer->containerShelfUnitStatuses()->delete();
                        $warhouseTrashedContainer->shelf->unit->delete();

                    }
                    
                    $warhouseTrashedContainer->shelf->rents()->delete();
                    $warhouseTrashedContainer->containerShelfStatuses()->delete();
                    $warhouseTrashedContainer->shelf->delete();

                }

                $warhouseTrashedContainer->rents()->delete();
                $warhouseTrashedContainer->containerStatuses()->delete();
                $warhouseTrashedContainer->forceDelete();

            }

        }
    }

}
