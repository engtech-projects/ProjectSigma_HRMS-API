<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Settings extends Model
{
    use HasApiTokens;
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
        return self::where('setting_name', $name)->first()->value;
    }

}
