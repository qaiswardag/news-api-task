<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'category_country', 'category_id', 'country_id');
    }
}
