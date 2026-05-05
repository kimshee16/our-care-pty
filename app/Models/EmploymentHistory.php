<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentHistory extends Model
{
    use HasFactory;

    protected $table = 'employment_history';

    protected $fillable = [
        'workers_id',
        'company_name',
        'job_position',
        'summary',
        'year_started',
        'year_ended',
        'is_currently_employed',
    ];

    public function worker()
    {
        return $this->belongsTo(HealthcareWorker::class, 'workers_id');
    }
}
