<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{
    protected $guarded = ['id'];
    protected $table = 'absens';
    protected $casts = [
        'jam_masuk'  => 'datetime' , 
        'jam_pulang' => 'datetime',
    ];
}
