<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    public $timestamps = false;
	protected $guarded = ['id'];

	public function resume()
    {
        return $this->hasOne(ApplicantResume::class, 'applicant_id', 'id');
    }

    public function jobs()
    {
        return $this->belongsToMany(CompanyJob::class, 'job_applicants', 'applicant_id', 'job_id');
    }
    
}
