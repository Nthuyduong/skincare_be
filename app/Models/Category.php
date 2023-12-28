<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Blog
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $feature_img
 * @property int $status
 */
class Category extends Model
{
    use HasFactory;

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
}
