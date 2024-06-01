<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Ingredient
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $content
 * @property string $featured_img
 * @property int $status
 * @property string $publish_date
 * @property string $suggest
 * @property string $slug
 * @property string $meta_title
 * @property string $meta_description
 */
class Ingredient extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'ingredients';

    // 1-n
    public function details()
    {
        return $this->hasMany(IngredientDetail::class, 'ingredient_id', 'id');
    }

}
