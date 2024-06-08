<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Like
 * @package App\Models
 * @property int $id
 * @property int $user_id
 * @property int $blog_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class Like extends Model
{

    use HasFactory;
    use SoftDeletes;

    protected $table = 'likes';

    protected $fillable = [
        'user_id',
        'blog_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}