<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class IngredientDetailTran
 * @package App\Models
 * @property int $id
 * @property string $locale
 * @property int $detail_id
 * @property string $name
 * @property string $content
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
*/

class IngredientDetailTran extends Model {
    use HasFactory;

    protected $table = 'ingredient_details_trans';

    use SoftDeletes;
}
