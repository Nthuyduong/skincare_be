<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class MailSetting
 * @package App\Models
 * @property int $id
 * @property int $type
 * @property string $title
 * @property string $content
 */
class MailSetting extends Model
{
    use HasFactory;
    protected $table = 'mail_settings';
    
    const TYPE_SUBSCRIBE = 1;
    const TYPE_CONTACT = 2;
    const TYPE_NOTIFICATION = 3;
    const TYPE_PORTFOLIO = 4;
    const TYPE_REPLY = 5;

    use SoftDeletes;
}