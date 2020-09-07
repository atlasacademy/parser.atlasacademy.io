<?php

namespace App;

use App\Jobs\ParseSubmissionJob;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Static_;

/**
 * App\Submission
 *
 * @property int $id
 * @property int $node_id
 * @property string $type
 * @property string $image
 * @property \App\SubmissionStatus $status
 * @property string|null $parse
 * @property int|null $parent_id
 * @property string|null $submission_uid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Submission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Submission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Submission query()
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereNodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereParse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereSubmissionUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Submission extends Model
{
    protected $table = 'submissions';

    public static function create(Node $node, string $type, string $image): self
    {
        $submission = new self();
        $submission->node_id = $node->id;
        $submission->type = $type;
        $submission->image = $image;
        $submission->save();

        return $submission;
    }

    public static function parse(Submission $submission)
    {
        $submission->status = SubmissionStatus::QUEUED();
        $submission->save();

        ParseSubmissionJob::dispatch($submission);
    }

    public function getStatusAttribute($value): SubmissionStatus
    {
        return new SubmissionStatus($value);
    }

    public function setStatusAttribute($value)
    {
        if ($value instanceof SubmissionStatus) {
            $value = $value->getValue();
        }

        $this->attributes['status'] = $value;
    }
}
