<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Subject;


class User extends Authenticatable
{
    
    use HasFactory, Notifiable;

     protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

     public function subjects(): HasMany
    {
    return $this->hasMany(Subject::class);
    }
      
    public function tasks(): HasMany
   {
    return $this->hasMany(Task::class);
   }

    public function studySessions(): HasMany
   {
    return $this->hasMany(StudySession::class);
    }
}
