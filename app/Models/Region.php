<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class Region extends Model
{
    use HasFactory, HasUlids, PostgisTrait;

    protected $postgisFields = [
        'geometry',
    ];

    protected $postgisTypes = [
        'geometry' => [
            'geomtype' => 'geometry',
            'srid' => 3857
        ],
    ];

    protected $table = 'regions';

    protected $guarded = [
        'id'
    ];
}
