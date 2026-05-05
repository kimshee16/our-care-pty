<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NdisRequirementCompleted extends Model
{
    protected $table = 'ndis_requirements_completed';

    public $timestamps = false;

    protected $fillable = [
        'worker_id',
        'parameter_id',
        'document_link',
    ];

    public function worker()
    {
        return $this->belongsTo(HealthcareWorker::class, 'worker_id');
    }

    public function parameter()
    {
        return $this->belongsTo(NdisRequirementParameter::class, 'parameter_id');
    }
}
