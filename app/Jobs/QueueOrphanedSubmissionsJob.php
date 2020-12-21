<?php

namespace App\Jobs;

use App\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class QueueOrphanedSubmissionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $submissions = Submission::query()
            ->whereBetween('status', [10, 19])
            ->orderBy('status', 'asc')
            ->limit(20)
            ->get();

        foreach ($submissions as $submission) {
            Submission::parse($submission);
        }
    }
}
