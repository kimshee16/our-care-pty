<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPostingKeySkill extends Model
{
    use HasFactory;

    protected $table = 'job_posting_key_skills';

    protected $fillable = [
        'job_posting_id',
        'skill',
    ];

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class, 'job_posting_id');
    }
}
