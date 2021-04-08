<?php

namespace App\Models;

use App\Traits\HasPermissionsTrait;
use Illuminate\Support\Facades\File;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use Notifiable, HasPermissionsTrait;

    protected $guard = 'admin';

    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'user_name', 'email', 'mobile', 'password',
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
        'email_verified_at' => 'datetime',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['profilePreview', 'roles', 'permissions'];

    /**
     * Set the user's first name.
     *
     * @param  string  $value
     * @return void
     */
    public function setProfilePreviewAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {
            
            $imagePath = 'uploads/admin/';

            if(!File::isDirectory($imagePath)){
                File::makeDirectory($imagePath, 0777, true, true);
            }

            $img = Image::make($encodedImageFile)->resize(100, 100);
            $img->save($imagePath.$this->id.'.jpg');

            $this->profilePreview()->updateOrCreate(
                ['user_id' => $this->id, 'user_type' => get_class($this)],
                ['preview' => $imagePath.$this->id.'.jpg']
            );

        }
    }

    /**
     * Get the user's image.
     */
    public function profilePreview()
    {
        return $this->morphOne(ProfilePreview::class, 'user')->withDefault();
    }
}
