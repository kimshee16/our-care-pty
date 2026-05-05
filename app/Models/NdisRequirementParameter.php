<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NdisRequirementParameter extends Model
{
    protected $table = 'ndis_requirements_parameters';

    public $timestamps = false;

    protected $fillable = [
        'requirements',
    ];

    public function completed()
    {
        return $this->hasMany(NdisRequirementCompleted::class, 'parameter_id');
    }
}
