<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function countries()
    {
        return $this->belongsToMany(Country::class, 'category_country', 'category_id', 'country_id');
    }
}
