<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'cities';

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $guarded = [
        'id'
    ];
}
