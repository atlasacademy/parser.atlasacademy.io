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
    private const MAX_CONCURRENT = 20;

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
        $count = Submission::query()
            ->where('status', '=', SubmissionStatus::PARSING()->getValue())
            ->count();

        if ($count > self::MAX_CONCURRENT) {
            // Too many pending submissions. Do not queue this submission yet
            return;
        }

        $parserAdapter->input($this->submission);

        Submission::populateParse($this->submission, null);
        $this->submission->status = SubmissionStatus::PARSING();
        $this->submission->save();

        FetchParseJob::dispatch($this->submission)->delay(now()->addSeconds(30));
    }
}
