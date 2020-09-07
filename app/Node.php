<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Node
 *
 * @property int $id
 * @property int $event_id
 * @property string $uid
 * @property string $name
 * @property int $qp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Drop[] $drops
 * @property-read int|null $drops_count
 * @method static \Illuminate\Database\Eloquent\Builder|Node newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Node newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Node query()
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereQp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Node whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Node extends Model
{
    protected $table = 'event_nodes';

    public function drops()
    {
        return $this->hasMany(Drop::class);
    }
}
