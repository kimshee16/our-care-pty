<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Endorsement;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, \Illuminate\Auth\MustVerifyEmail;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'fullname',
        'email',
        'phone',
        'password',
        'accounttype',
        'record_id',
        'verified',
        'approved',
        'approved_by',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function healthcareWorker()
    {
        return $this->hasOne(HealthcareWorker::class);
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'record_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'user_applied_id');
    }

    public function endorsements()
    {
        return $this->hasMany(Endorsement::class, 'worker_id');
    }

    public function endorsementsCreated()
    {
        return $this->hasMany(Endorsement::class, 'endorsed_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
