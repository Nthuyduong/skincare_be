<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BlogDetail
 * @package App\Models
 * @property int $id
 * @property int $blog_id
 * @property string $content
 * @property string $content_draft
 */
class BlogDetail extends Model
{
    use HasFactory;

    protected $table = 'blog_details';

    protected $fillable = [
        'blog_id',
        'content',
        'content_draft'
    ];

    use SoftDeletes;

    public function blog() {
        return $this->belongsTo(Blog::class, 'blog_id', 'id');
    }
}