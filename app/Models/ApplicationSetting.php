<?php

namespace App\Models;

use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\ImageManagerStatic as Image;
use Intervention\Image\Exception\NotReadableException;

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
    
    public function medias()
    {
        return $this->hasMany(Media::class, 'application_setting_id', 'id');
    }

    public function setApplicationMediaAttribute($medias)
    {
        $this->medias()->delete();

        foreach ($medias as $mediaKey => $media) {
            
            $newMedia = new Media();
            $newMedia->name = $media->name;
            $newMedia->setMediaLogo($media->logo, str_replace(' ', '_', $media->name));
            $newMedia->link = $media->link;
            $newMedia->save();

        }
    }

    public function setApplicationLogoAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {
            
            $imagePath = 'uploads/application/';

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

            $img = $img->resize(50, 50);
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

            try 
            {
                $img = Image::make($encodedImageFile);
            }
            catch(NotReadableException $e)
            {
                // If error, stop and return
                return;
            }

            $img = $img->resize(32, 32);
            $img->save($imagePath.'application_favicon.png', 100);

            $this->attributes['application_favicon'] = $imagePath.'application_favicon.png';

        }
    } 
  
}
