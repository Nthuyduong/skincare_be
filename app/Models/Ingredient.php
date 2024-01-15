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
 * @property string $content
 * @property string $feature_img
 * @property int $status
 */
class Ingredient extends Model
{
    use HasFactory;

    protected $table = 'ingredients';

}
