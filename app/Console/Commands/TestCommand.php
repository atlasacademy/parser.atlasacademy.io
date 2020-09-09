<?php

namespace App\Console\Commands;

use App\Jobs\CheckParseResultJob;
use App\Jobs\ExportSubmissionJob;
use App\Jobs\FetchParseJob;
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
//        Submission::parse(Submission::find(2));
//        FetchParseJob::dispatch(Submission::find(3));
//        CheckParseResultJob::dispatchNow(Submission::find(2));
        ExportSubmissionJob::dispatchNow([1]);

        return 0;
    }
}
