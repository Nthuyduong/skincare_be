<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Blog
 * @package App\Models
 * @property int $id
 * @property string $content
 * @property string $content_draft
 * @property string $summary
 * @property string $tag
 * @property string $slug
 * @property int $status
 * @property int $view_count
 * @property int $comment_count
 * @property string $featured_image
 */
class Blog extends Model
{
    use HasFactory;

    protected $table = 'blogs';

    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;

    use SoftDeletes;

    // n-n
    public function categories() {
        return $this->belongsToMany(Category::class, 'blogs_categories', 'blog_id', 'category_id');
    }
}
