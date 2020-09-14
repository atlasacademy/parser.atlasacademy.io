<?php

namespace App\Jobs;

use App\Parser\ParseWrapper;
use App\Submission;
use App\SubmissionStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AppendSubmissionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $nodeIds = $this->submissions()
            ->distinct()
            ->pluck('node_id');

        foreach ($nodeIds as $nodeId) {
            $this->appendSubmissionsForNode($nodeId);
        }
    }

    private function appendSubmissionsForNode(int $id)
    {
        $submissions = $this->submissions()
            ->where('node_id', '=', $id)
            ->get()
            ->all();

        $matchedIds = [];
        $matches = [];

        foreach ($submissions as $submission) {
            [$matched, $ids] = $this->findMatches($submission, $submissions, $matchedIds);

            if ($matched) {
                $matchedIds = array_merge($matchedIds, $ids);
                $matches[$submission->id] = $ids;
            }
        }

        foreach ($matches as $id => $matchIds) {
            $submissionIds = array_merge([$id], $matchIds);

            ExportSubmissionJob::dispatch($submissionIds);
        }
    }

    private function submissions(): Builder
    {
        return Submission::query()
            ->where('type', '=', 'full')
            ->where('status', '=', SubmissionStatus::ERROR_MISSING_DROPS()->getValue());
    }

    /**
     * @param Submission $submission
     * @param Submission[] $submissions
     * @param int[] $matchedIds
     * @param ParseWrapper|null $parseWrapper
     * @return array
     */
    private function findMatches(Submission $submission, array $submissions, array $matchedIds, ?ParseWrapper $parseWrapper = null): array
    {
        if (!$parseWrapper)
            $parseWrapper = ParseWrapper::create($submission);

        foreach ($submissions as $appendSubmission) {
            // don't match itself
            if ($submission->id === $appendSubmission->id)
                continue;

            // if already matched
            if (in_array($appendSubmission->id, $matchedIds))
                continue;

            // if submitter doesn't match
            if ($appendSubmission->submitter !== $submission->submitter)
                continue;

            $append = ParseWrapper::create($appendSubmission);

            // total qp doesn't match
            if ($parseWrapper->totalQp() !== $append->totalQp())
                continue;

            // drop count doesn't match
            if ($parseWrapper->dropCount() !== $append->dropCount())
                continue;

            // scroll position is greater than append only if both scroll positions are valid
            if ($parseWrapper->scrollPosition() !== -1.0
                && $append->scrollPosition() !== -1.0
                && $parseWrapper->scrollPosition() > $append->scrollPosition())
                continue;

            $lastLine = $parseWrapper->lastLine();
            $lastDropLine = $parseWrapper->dropLine($lastLine);
            $matchingLine = $append->getMatchingLine($lastDropLine);

            // matching line not found
            if ($matchingLine === null)
                continue;

            // merge the submissions
            $parseWrapper->append($append);

            // if complete
            if (!$parseWrapper->hasMissingDrops()) {
                return [true, [$appendSubmission->id]];
            }

            // recurse call
            $tempMatchedIds = $matchedIds;
            $tempMatchedIds[] = $appendSubmission->id;
            [$matched, $ids] = $this->findMatches($submission, $submissions, $tempMatchedIds, $parseWrapper);

            if ($matched) {
                return [true, array_merge([$appendSubmission->id], $ids)];
            }

            return [false, []];
        }

        return [false, []];
    }
}
