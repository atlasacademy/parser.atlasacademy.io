<?php

namespace App;

use App\Jobs\ParseSubmissionJob;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Submission
 *
 * @property int $id
 * @property int $node_id
 * @property string $type
 * @property string $image
 * @property string $submitter
 * @property \App\SubmissionStatus $status
 * @property string|null $parse
 * @property int|null $export_id
 * @property int|null $export_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Node $node
 * @method static \Illuminate\Database\Eloquent\Builder|Submission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Submission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Submission query()
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereExportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereExportOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereNodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereParse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereSubmitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Submission extends Model
{
    protected $table = 'submissions';

    public static function create(Node $node, string $type, string $image, string $submitter): self
    {
        $submission = new self();
        $submission->node_id = $node->id;
        $submission->type = $type;
        $submission->image = $image;
        $submission->submitter = $submitter;
        $submission->save();

        return $submission;
    }

    public static function parse(Submission $submission)
    {
        $submission->status = SubmissionStatus::QUEUED();
        $submission->save();

        ParseSubmissionJob::dispatch($submission);
    }

    public function node()
    {
        return $this->belongsTo(Node::class);
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
