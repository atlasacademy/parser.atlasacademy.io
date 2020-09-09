<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Export
 *
 * @property int $id
 * @property int $node_id
 * @property string $type
 * @property string $payload
 * @property string $parse
 * @property string $submitter
 * @property string|null $receipt
 * @property string|null $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Node $node
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Submission[] $submissions
 * @property-read int|null $submissions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Export newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Export newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Export query()
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereNodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereParse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereReceipt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereSubmitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Export extends Model
{

    protected $table = "exports";

    public function node()
    {
        return $this->belongsTo(Node::class);
    }

    public function submissions()
    {
        return $this
            ->hasMany(Submission::class)
            ->orderBy('export_order', 'ASC');
    }
}
