<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;


    protected $table = 'sub_categories';


    protected $fillable = [
        'category',
        'subcategory',
        'color',
    ];

    /**
     * If you need to define any relationships, for example, if subcategories
     * belong to a category, you could add the following:
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
