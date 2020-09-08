<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\TemplateMap
 *
 * @property int $id
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMap newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMap newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMap query()
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMap whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMap whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMap whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMap whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TemplateMap extends Model
{
    public $incrementing = false;
    protected $table = "template_maps";

    private static $maps = [];

    public static function getValue(int $id, string $default): string
    {
        if (array_key_exists($id, self::$maps)) {
            $value = self::$maps[$id];

            return $value === null ? $default : $value;
        }

        $map = static::query()->where('id', '=', $id)->first();
        if (!$map) {
            self::$maps[$id] = null;

            return $default;
        }

        return self::$maps[$id] = $map->code;
    }

    public static function hasValue(int $id): bool
    {
        if (array_key_exists($id, self::$maps)) {
            $value = self::$maps[$id];

            return $value !== null;
        }

        $map = static::query()->where('id', '=', $id)->first();
        if (!$map) {
            self::$maps[$id] = null;

            return false;
        }

        self::$maps[$id] = $map->code;

        return true;
    }
}
