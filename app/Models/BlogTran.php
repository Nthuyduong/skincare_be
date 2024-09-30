<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Blog
 * @package App\Models
 * @property int $id
 * @property string $locale
 * @property string $blog_id
 * @property string $title
 * @property string $summary
 * @property string $tag
 * @property string $slug
 * @property string $meta_title
 * @property string $meta_description
 * @property string $excerpt
 * @property string $suggest
 * @property string $content
 * @property string $content_draft
 * @property int $estimate_time
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */

class BlogTran extends Model {
   use HasFactory;

   protected $table = 'blog_trans';

   use SoftDeletes;
}