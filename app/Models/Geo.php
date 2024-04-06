<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class Geo extends Model
{
    use HasFactory, HasUlids, PostgisTrait;

    protected $table = 'geo';

    protected $postgisFields = [
        'geometry',
    ];

    protected $postgisTypes = [
        'geometry' => [
            'geomtype' => 'geometry',
            'srid' => 3857
        ],
    ];

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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function getTypeAttribute()
    {
        return 'Feature';
    }

    public function getPropertiesAttribute()
    {
        return [
            'name' => $this->name,
            'category' => $this->category->title
        ];
    }
}
