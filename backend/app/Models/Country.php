<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public function languages()
    {
        return $this->belongsToMany(Language::class, 'country_language', 'country_id', 'language_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_country', 'country_id', 'category_id');
    }
}
