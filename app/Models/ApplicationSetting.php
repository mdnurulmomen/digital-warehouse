<?php

namespace App\Models;

use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\ImageManagerStatic as Image;

class ApplicationSetting extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    /**
     * Set the user's first name.
     *
     * @param  string  $value
     * @return void
     */
    public function setApplicationLogoAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {
            
            $imagePath = 'uploads/application/';

            if(!File::isDirectory($imagePath)){
                File::makeDirectory($imagePath, 0777, true, true);
            }

            $img = Image::make($encodedImageFile)->resize(50, 50);
            $img->save($imagePath.'application_logo.png', 100);

            $this->attributes['application_logo'] = $imagePath.'application_logo.png';

        }
    }

	/**
     * Set the user's first name.
     *
     * @param  string  $value
     * @return void
     */
    public function setApplicationFaviconAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {
            
            $imagePath = 'uploads/application/';

            if(!File::isDirectory($imagePath)){
                File::makeDirectory($imagePath, 0777, true, true);
            }

            $img = Image::make($encodedImageFile)->resize(32, 32);
            $img->save($imagePath.'application_favicon.png', 100);

            $this->attributes['application_favicon'] = $imagePath.'application_favicon.png';

        }
    }    
}
