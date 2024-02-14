<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Image
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $name
 * @property string $type
 * @property string $size
 * @property string $alt
 * @property string $url
 * @property string $suggest
 */

class Image extends Model {
    use HasFactory;

    protected $table = 'images';

    use SoftDeletes;
}