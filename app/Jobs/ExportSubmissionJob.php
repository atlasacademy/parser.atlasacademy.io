<?php

namespace App\Jobs;

use App\Export;
use App\Parser\ParseWrapper;
use App\Submission;
use App\Submission\SubmissionApi;
use App\Submission\SubmissionExport;
use App\SubmissionStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use InvalidArgumentException;

class ExportSubmissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    private $ids;

    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    public function handle(SubmissionApi $submissionApi)
    {
        $submissions = $this->getSubmissions();
        $node = $submissions[0]->node;
        $type = $submissions[0]->type;
        $parseWrapper = $this->makeParseWrapper($submissions);
        $submissionExport = $this->makeSubmissionExport($parseWrapper, $submissions);

        $export = new Export();
        $export->node_id = $node->id;
        $export->type = $type;
        $export->payload = json_encode($submissionExport->toArray());
        $export->parse = json_encode($parseWrapper->toArray());
        $export->submitter = $submissionExport->submitter();
        $export->save();

        foreach ($submissions as $k => $submission) {
            $submission->status = count($submissions) > 1
                ? SubmissionStatus::SUCCESS_MULTIPLE()->getValue()
                : SubmissionStatus::SUCCESS()->getValue();
            $submission->export_id = $export->id;
            $submission->export_order = $k;
            $submission->save();
        }

        [$receipt, $missingDrops] = $submissionApi->submitRun($submissionExport);
        $export->receipt = $receipt;
        $export->token = $submissionExport->token();
        $export->save();

        if ($missingDrops) {
            ImportEventJob::dispatch($node->event);
        }
    }

    /**
     * @return Submission[]
     */
    private function getSubmissions(): array
    {
        if (!count($this->ids)) {
            throw new InvalidArgumentException('Missing Submission IDs');
        }

        return array_map(function (int $id): Submission {
            $submission = Submission::query()->where('id', '=', $id)->first();
            if (!$submission) {
                throw new InvalidArgumentException('Invalid Submission ID');
            }

            return $submission;
        }, $this->ids);
    }

    /**
     * @param Submission[] $submissions
     * @return ParseWrapper
     */
    private function makeParseWrapper(array $submissions): ParseWrapper
    {
        $parseWrapper = ParseWrapper::create($submissions[0]);
        foreach (array_slice($submissions, 1) as $submission) {
            $parseWrapper->append(ParseWrapper::create($submission));
        }

        return $parseWrapper;
    }

    /**
     * @param ParseWrapper $parseWrapper
     * @param Submission[] $submissions
     * @return SubmissionExport
     */
    private function makeSubmissionExport(ParseWrapper $parseWrapper, array $submissions): SubmissionExport
    {
        $node = $submissions[0]->node;
        $submitter = $submissions[0]->submitter;
        $type = $submissions[0]->type;

        $submissionExport = new SubmissionExport($node, $type, $submitter);
        foreach ($node->drops as $drop) {
            $submissionExport->addDrop($drop, $parseWrapper->getCountForDrop($drop));
        }

        return $submissionExport;
    }
}
