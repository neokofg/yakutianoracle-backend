<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class City extends Model
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

    protected $table = 'cities';

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $guarded = [
        'id'
    ];

    protected $appends = [
        'type',
        'properties'
    ];


    public function year_rounds(): HasMany
    {
        return $this->hasMany(YearRound::class, 'city_id', 'id');
    }

    public function transports(): HasMany
    {
        return $this->hasMany(Transport::class, 'city_id', 'id');
    }

    public function getTypeAttribute()
    {
        return 'Feature';
    }
}
