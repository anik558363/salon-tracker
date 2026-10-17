<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    // দ্রুত রিড করার জন্য হেল্পার মেথড (Cache Supported)
    public static function get($key, $default = null)
    {
        $settings = Cache::rememberForever('app_settings', function () {
            return self::pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    // সেভ বা আপডেট করার পর ক্যাশ ক্লিয়ার করা
    public static function set($key, $value)
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('app_settings');
    }
}
