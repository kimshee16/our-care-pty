<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Skill;
use App\Models\EmploymentHistory;
use App\Models\NdisRequirementCompleted;

class HealthcareWorker extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profession',
        'specialization',
        'license_number',
        'experience_years',
        'facility_name',
        'facility_address',
        'location',
        'credentials',
        'profile_photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skills()
    {
        return $this->hasMany(Skill::class, 'workers_id');
    }

    public function employmentHistory()
    {
        return $this->hasMany(EmploymentHistory::class, 'workers_id');
    }

    public function ndisRequirementsCompleted()
    {
        return $this->hasMany(NdisRequirementCompleted::class, 'worker_id');
    }
}
