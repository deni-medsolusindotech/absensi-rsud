<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [
        'id'
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
    public function jadwals()
    {
        return $this->hasMany(Schedule::class, 'user_id');
    }

    public function masuk()
    {
        return $this->hasMany(Masuk::class, 'user_id');
    }

    public function pulang()
    {
        return $this->hasMany(Pulang::class, 'user_id');
    }

    public function masuknow()
    {
        return $this->hasMany(Masuk::class, 'user_id')->whereDate('created_at', now())->first();
    }
    public function pulangnow()
    {
        return $this->hasMany(Pulang::class, 'user_id')->whereDate('created_at', now())->first();
    }
    public function allabsen()
    {
        return $this->hasMany(Absen::class, 'user_id');
    }
    public function absen()
    {
        $data = $this->hasMany(Absen::class, 'user_id');
        if ($data){
            $data = $data->whereDate('created_at', now())->latest()->first();
        }
        return $data;
    }
    
}
