<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories');
    }

    // Define relationships if a category have a parent category
    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_category_id');
    }

    // Define relationships if you have child categories
    public function subCategories()
    {
        return $this->hasMany(Category::class, 'parent_category_id');
    }
}
