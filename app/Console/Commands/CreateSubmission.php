<?php

namespace App\Console\Commands;

use App\Event;
use App\Submission;
use Illuminate\Console\Command;

class CreateSubmission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parser:create-submission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Submission';

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
        $this->output->writeln('Events');
        $this->output->table(
            ['uid', 'name'],
            Event::query()
                ->orderBy('uid', 'ASC')
                ->select(['uid', 'name'])
                ->get()
                ->toArray()
        );

        $eventUid = $this->output->ask('Input Event Uid');
        $event = Event::query()->where('uid', '=', $eventUid)->first();
        if (!$event) {
            $this->output->warning('Event not found');

            return 0;
        }

        $this->output->writeln('Nodes');
        $this->output->table(
            ['uid', 'name'],
            $event->nodes()->select(['uid', 'name'])->get()->toArray()
        );

        $nodeUid = $this->output->ask('Input Node Uid');
        $node = $event->nodes()->where('uid', '=', $nodeUid)->first();
        if (!$node) {
            $this->output->warning('Node not found');

            return 0;
        }

        $type = $this->output->choice('Type', ['simple', 'full']);

        $image = $this->output->ask('Input source');
        if (!$image) {
            $this->output->warning('Image is required');

            return 0;
        }

        $submitter = $this->output->ask('Submitter');
        if (!$submitter) {
            $this->output->warning('Submitter is required');

            return 0;
        }

        $submission = Submission::create($node, $type, $image, $submitter);
        $this->output->success('Successfully created submission: ' . $submission->id);

        return 0;
    }
}
