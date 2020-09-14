<?php

namespace App\Jobs;

use App\Parser\ParserAdapter;
use App\Submission;
use App\SubmissionStatus;
use File;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ParseSubmissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Submission
     */
    private $submission;

    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
    }

    public function handle(ParserAdapter $parserAdapter)
    {
        $parserAdapter->input($this->submission);

        $this->submission->status = SubmissionStatus::PARSING();
        $this->submission->parse = null;
        $this->submission->parse_hash = null;
        $this->submission->drop_count = null;
        $this->submission->qp_total = null;
        $this->submission->save();

        FetchParseJob::dispatch($this->submission)->delay(now()->addSeconds(30));
    }
}
