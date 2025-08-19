<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Settings extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'setting_name',
        'value',
    ];
    /**
     *
     * DYNAMIC SCOPES
     *
     */
    public function scopeSettingName($query, $name)
    {
        return $query->where('setting_name', $name);
    }
    /**
     *
     * MODEL FUNCTIONS
     *
     */

    public static function getSettingValue($name)
    {
        return self::settingName($name)->first()?->value;
    }
}
