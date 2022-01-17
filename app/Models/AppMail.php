<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppMail extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function recipients()
    {
    	return $this->hasMany(AppMailRecipient::class, 'app_mail_id', 'id');
    }

    public function sender()
    {
    	return $this->morphTo();
    }
}
