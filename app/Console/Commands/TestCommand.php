<?php

namespace App\Console\Commands;

use App\Jobs\AppendSubmissionsJob;
use App\Jobs\CheckParseResultJob;
use App\Jobs\ExportSubmissionJob;
use App\Jobs\FetchParseJob;
use App\Jobs\ParseSubmissionJob;
use App\Parser\ParseWrapper;
use App\Submission;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parser:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ParseSubmissionJob::dispatchNow(Submission::find(1420));
//        FetchParseJob::dispatchNow(Submission::find(909));
//        CheckParseResultJob::dispatchNow(Submission::find(1089));
//        ExportSubmissionJob::dispatchNow([907]);
//        AppendSubmissionsJob::dispatchNow();

        return 0;
    }
}
