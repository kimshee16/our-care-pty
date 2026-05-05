<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'workers_id',
        'skill',
    ];

    public function worker()
    {
        return $this->belongsTo(HealthcareWorker::class, 'workers_id');
    }
}
