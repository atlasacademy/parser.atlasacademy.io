<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Export
 *
 * @property int $id
 * @property string $uid
 * @property string $payload
 * @property string $parse
 * @property string $submitter
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Submission[] $submissions
 * @property-read int|null $submissions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Export newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Export newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Export query()
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereParse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereSubmitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Export extends Model
{

    protected $table = "exports";

    public function submissions()
    {
        return $this
            ->hasMany(Submission::class)
            ->orderBy('export_order', 'ASC');
    }
}
