<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Endorsement;

class JobPosting extends Model
{
    use HasFactory;

    protected $table = 'job_posting';

    protected $fillable = [
        'title',
        'description',
        'minimum_pay_offer',
        'maximum_pay_offer',
        'client_id',
        'location',
        'employment_type',
        'experience',
        'specialty',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function requirements()
    {
        return $this->hasMany(JobPostingKeyRequirement::class, 'job_posting_id');
    }

    public function keySkills()
    {
        return $this->hasMany(JobPostingKeySkill::class, 'job_posting_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'job_posting_id');
    }

    public function endorsements()
    {
        return $this->hasMany(Endorsement::class, 'job_post_id');
    }
}
