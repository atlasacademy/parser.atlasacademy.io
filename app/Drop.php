<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Drop
 *
 * @property int $id
 * @property int $node_id
 * @property string $uid
 * @property int $quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Drop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Drop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Drop query()
 * @method static \Illuminate\Database\Eloquent\Builder|Drop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Drop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Drop whereNodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Drop whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Drop whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Drop whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Drop extends Model
{
    protected $table = 'event_node_drops';

    public function isEventOnly(): bool
    {
        return $this->uid[0] === "E";
    }
}
