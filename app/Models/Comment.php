<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Comment
 * @package App\Models
 * @property int $id
 * @property int $user_id
 * @property int $blog_id
 * @property string $content
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class Comment extends Model
{

    use HasFactory;
    use SoftDeletes;

    const STATUS_SHOW = 0;
    const STATUS_HIDE = 1;

    const TYPE_USER = 0;
    const TYPE_GUEST = 1;

    protected $table = 'comments';

    protected $fillable = [
        'user_id',
        'blog_id',
        'content',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}