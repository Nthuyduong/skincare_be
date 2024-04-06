<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Contact
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $message
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class Contact extends Model
{

    use HasFactory;
    use SoftDeletes;

    protected $table = 'contacts';

    const STATUS_UNREAD = 0;
    const STATUS_READ = 1;

    protected $fillable = [
        'name',
        'email',
        'message',
        'status',
    ];
}