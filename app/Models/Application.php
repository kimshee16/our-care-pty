<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_posting_id',
        'user_applied_id',
        'application_details',
        'expected_salary',
        'attachments',
        'metric_score',
        'interview_status',
        'interview_date',
        'interview_location',
        'interview_notes',
        'reschedule_reason',
        'additional_notes',
    ];

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function applicant()
    {
        return $this->belongsTo(User::class, 'user_applied_id');
    }
}
