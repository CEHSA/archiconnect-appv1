<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saved(function ($setting) {
            Cache::forget('app_settings'); // Clear cache on save
            Cache::forget('setting_' . $setting->key);
        });

        static::deleted(function ($setting) {
            Cache::forget('app_settings'); // Clear cache on delete
            Cache::forget('setting_' . $setting->key);
        });
    }

    /**
     * Get a specific setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = Cache::rememberForever('setting_' . $key, function () use ($key) {
            return self::where('key', $key)->first();
        });

        if ($setting) {
            return self::castValue($setting->value, $setting->type);
        }

        return $default;
    }

    /**
     * Get all settings, grouped by their 'group' attribute.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getAllGrouped()
    {
        return Cache::rememberForever('app_settings_grouped', function () {
            return self::all()->mapToGroups(function ($item) {
                return [$item['group'] => $item];
            });
        });
    }

    /**
     * Cast the setting value to its defined type.
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected static function castValue($value, string $type)
    {
        switch ($type) {
            case 'integer':
                return (int) $value;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                return json_decode($value, true);
            default: // string, text
                return $value;
        }
    }
}
