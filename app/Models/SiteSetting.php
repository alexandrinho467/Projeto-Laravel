<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['key','value'];

    public static function get(string $key, string $default = ''): string {
        $setting = static::where('key', $key)->first();
        if (! $setting || trim((string) $setting->value) === '') {
            return $default;
        }
        return (string) $setting->value;
    }

    public static function set(string $key, string $value): void {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
