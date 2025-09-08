<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $guarded = ['id'];
    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
            'tanggal_pulang' => 'date',
        ];
    }
}
