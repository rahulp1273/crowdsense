<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $fillable = [
        'name', 'location', 'type', 'current_crowd_count',
        'lat', 'lng', 'radius', 'max_capacity', 'is_chat_enabled',
        'pincode', 'address', 'country', 'city', 'state', 'street', 'mansion'
    ];

    public function checkIns()
    {
        return $this->hasMany(CheckIn::class);
    }
}
