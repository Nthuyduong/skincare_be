<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Blog
 * @package App\Models
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $image
 */
class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

}
