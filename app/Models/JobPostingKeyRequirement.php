<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPostingKeyRequirement extends Model
{
    use HasFactory;

    protected $table = 'job_posting_key_requirements';

    protected $fillable = [
        'job_posting_id',
        'description',
    ];

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class, 'job_posting_id');
    }
}
