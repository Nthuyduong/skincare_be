<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Blog
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $featured_img
 * @property int $status
 */
class Category extends Model
{
    use HasFactory;

    use SoftDeletes;
    protected $table = 'categories';

    // 1-n
    public function childrens()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    // 1-1
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }

    public function blogs() {
        return $this->belongsToMany(Blog::class, 'blogs_categories', 'category_id', 'blog_id');
    }

    public function vi() {
        return $this->hasOne(CategoryTran::class, 'category_id', 'id')->where('locale', 'vi');
    }

    public function locales() {
        return $this->hasMany(CategoryTran::class, 'category_id', 'id');
    }
}
