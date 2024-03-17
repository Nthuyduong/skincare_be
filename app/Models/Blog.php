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
 * @property string $banner_image
 * @property string $meta_title
 * @property string $meta_description
 * @property string $author
 * @property string $publish_date
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $excerpt
 * @property string $estimate_time
 */
class Blog extends Model
{
    use HasFactory;

    protected $table = 'blogs';
    
    const STATUS_HIDDEN = 0;
    const STATUS_SHOW = 1;

    use SoftDeletes;

    // n-n
    public function categories() {
        return $this->belongsToMany(Category::class, 'blogs_categories', 'blog_id', 'category_id');
    }

    public function detail() {
        return $this->hasOne(BlogDetail::class, 'blog_id', 'id');
    }
}
