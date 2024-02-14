<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class IngredientDetail
 * 
 */
class IngredientDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'ingredient_details';
}