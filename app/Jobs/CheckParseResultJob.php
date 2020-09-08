<?php

namespace App\Jobs;

use App\Node;
use App\Parser\ParseWrapper;
use App\Submission;
use App\SubmissionStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckParseResultJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Submission
     */
    private $submission;
    /**
     * @var Node
     */
    private $node;
    /**
     * @var ParseWrapper
     */
    private $parseWrapper;

    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->submission->status->equals(SubmissionStatus::PARSED())) {
//            return;
        }

        $node = Node::query()
            ->with('drops')
            ->where('id', '=', $this->submission->node_id)
            ->first();

        $parseWrapper = new ParseWrapper($this->submission->parse);

        if (!$parseWrapper->isValid()) {
            $this->submission->status = SubmissionStatus::ERROR_FAILURE();
            $this->submission->save();

            return;
        }

        if (!$node) {
            $this->submission->status = SubmissionStatus::ERROR_MISSING_NODE();
            $this->submission->save();

            return;
        }

        if ($parseWrapper->hasUnknownDrops()) {
            $this->submission->status = SubmissionStatus::ERROR_UNKNOWN_DROPS();
            $this->submission->save();

            return;
        }

        if ($parseWrapper->hasInvalidDrops($node)) {
            $this->submission->status = SubmissionStatus::ERROR_INVALID_DROPS();
            $this->submission->save();

            return;
        }

        if ($parseWrapper->hasMissingDrops($this->submission->type === 'full')) {
            $this->submission->status = SubmissionStatus::ERROR_MISSING_DROPS();
            $this->submission->save();

            return;
        }

        if ($node->qp !== $parseWrapper->questQp()) {
            $this->submission->status = SubmissionStatus::ERROR_QP_MISMATCH();
            $this->submission->save();

            return;
        }

        ExportSubmissionJob::dispatch([$this->submission->id]);
    }

}
