<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name', 'color','created_by'
    ];

    // public function users()
    // {
    //     return $this->belongsToMany('App\Models\User', 'user_categories', 'category_id','user_id');
    // }

    public function users()
    {
        return $this->hasMany(User::class, 'category_id');
    }


    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }

}
