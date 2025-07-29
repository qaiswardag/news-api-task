<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    public function countries()
    {
        return $this->belongsToMany(Country::class, 'country_language', 'language_id', 'country_id');
    }
}
