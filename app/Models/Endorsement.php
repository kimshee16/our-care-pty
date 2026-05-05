<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\JobPosting;
use App\Models\User;
use App\Models\Client;

class Endorsement extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'job_post_id',
        'client_id',
        'meet_and_greet_date',
        'meet_and_greet_link',
        'endorsed_by',
    ];

    protected $casts = [
        'meet_and_greet_date' => 'datetime',
    ];

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class, 'job_post_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'endorsed_by');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
