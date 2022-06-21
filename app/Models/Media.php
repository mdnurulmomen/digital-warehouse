<?php

namespace App\Models;

use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\ImageManagerStatic as Image;
use Intervention\Image\Exception\NotReadableException;

class Media extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function setMediaLogo($encodedImageFile, $mediaName)
    {
        if ($encodedImageFile) {
            
            $imagePath = 'website/media/';

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
            $img->save($imagePath."$mediaName.png", 100);

            $this->attributes['logo'] = $imagePath."$mediaName.png";

        }
    } 
}
