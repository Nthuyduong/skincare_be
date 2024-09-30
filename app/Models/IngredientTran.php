<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class IngredientTran
 * @package App\Models
 * @property int $id
 * @property string $locale
 * @property int $ingredient_id
 * @property string $name
 * @property string $slug
 * @property string $meta_title
 * @property string $meta_description
 * @property string $description
 * @property string $content
 * @property string $suggest
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */

class IngredientTran extends Model {
    use HasFactory;

    protected $table = 'ingredient_trans';

    use SoftDeletes;
}