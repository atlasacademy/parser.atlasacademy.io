<?php

namespace App\Jobs;

use App\Parser\ParserAdapter;
use App\Submission;
use App\SubmissionStatus;
use File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class FetchParseJob implements ShouldQueue
{
    private const MAX_ATTEMPTS = 6;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Submission
     */
    private $submission;
    /**
     * @var int
     */
    private $attempts;

    public function __construct(Submission $submission, int $attempts = 0)
    {
        $this->submission = $submission;
        $this->attempts = $attempts;
    }

    public function handle(ParserAdapter $parserAdapter)
    {
        if (!$parserAdapter->hasOutput($this->submission)) {
            if ($this->attempts < self::MAX_ATTEMPTS) {
                static::dispatch($this->submission, $this->attempts+1)->delay(now()->addSeconds(30));
            }

            return;
        }

        $json = $parserAdapter->getOutput($this->submission);
        $parserAdapter->emptyOutput($this->submission);

        $this->submission->parse = $json;
        $this->submission->status = SubmissionStatus::PARSED();
        $this->submission->save();

        CheckParseResultJob::dispatch($this->submission);
    }
}
