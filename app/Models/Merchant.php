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

class Merchant extends Authenticatable
{
    use Notifiable, SoftDeletes, HasPermissionsTrait;

    protected $guard = 'merchant';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'user_name', 'email', 'mobile', 'password', 'active'
    ];

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
    protected $with = ['profilePreview'];

    /**
     * Get the user's image.
     */
    public function profilePreview()
    {
        return $this->morphOne(ProfilePreview::class, 'user')->withDefault();
    }
    
    public function spaceDeals()
    {
        return $this->hasMany(MerchantSpaceDeal::class, 'merchant_id', 'id');
    }

    public function supportDeal()
    {
        return $this->hasOne(MerchantSupportDeal::class, 'merchant_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(MerchantProduct::class, 'merchant_id', 'id');
    }

    public function requirements()
    {
        return $this->hasMany(MerchantRequirement::class, 'merchant_id', 'id');
    }
    

    /**
     * Set the user's first name.
     *
     * @param  string  $value
     * @return void
     */
    public function setProfilePreviewAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {
            
            $imagePath = 'storage/merchants/';

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

            $img = $img->resize(300, 300);  // when facebook uses 180*180
            $img->save($imagePath.$this->id.'.jpg', 100);

            $this->profilePreview()->updateOrCreate(
                ['user_id' => $this->id, 'user_type' => get_class($this)],
                ['preview' => $imagePath.$this->id.'.jpg']
            );

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
    
}
