<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\TemplateMap
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMap newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMap newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMap query()
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMap whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMap whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMap whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMap whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateMap whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TemplateMap extends Model
{
    protected $table = "template_maps";

    public static function getValue(string $name, string $default): string
    {
        $map = static::query()->where('name', '=', $name)->first();

        return $map ? $map->code : $default;
    }

    public static function hasValue(string $name): bool
    {
        $count = static::query()->where('name', '=', $name)->count();

        return $count > 0;
    }
}
