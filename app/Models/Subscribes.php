<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Subscribes
 * @package App\Models
 * @property int $id
 * @property string $email
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class Subscribes extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'subscribes';

    protected $fillable = [
        'email',
    ];
}