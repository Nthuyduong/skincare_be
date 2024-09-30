<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Category tran
 * @package App\Models
 * @property int $id
 * @property string $locale
 * @property int $category_id
 * @property string $name
 * @property string $slug
 * @property string $meta_title
 * @property string $meta_description
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */

class CategoryTran extends Model {
    use HasFactory;

    protected $table = 'category_trans';

    use SoftDeletes;
}