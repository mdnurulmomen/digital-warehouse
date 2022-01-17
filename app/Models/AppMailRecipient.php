<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppMailRecipient extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function mail()
    {
    	return $this->belongsTo(AppMail::class, 'app_mail_id', 'id');
    }
}
