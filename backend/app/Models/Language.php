<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Language extends Model
{
    use HasFactory;

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'country_language', 'language_id', 'country_id');
    }
}
