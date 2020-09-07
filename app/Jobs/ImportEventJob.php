<?php

namespace App\Jobs;

use App\Drop;
use App\Event;
use App\Node;
use App\Submission\SubmissionApi;
use App\Submission\SubmissionNode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Event
     */
    private $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(DatabaseManager $databaseManager, SubmissionApi $submissionApi)
    {
        $submissionsEvent = $submissionApi->event($this->event->uid);

        $databaseManager->beginTransaction();

        foreach ($submissionsEvent->nodes() as $submissionNode) {
            $node = $this->getNode($submissionNode);
            $this->syncDrops($node, $submissionNode);
        }

        $databaseManager->commit();
    }

    private function getNode(SubmissionNode $submissionNode): Node
    {
        $node = $this->event->nodes()->where('uid', '=', $submissionNode->uid())->first();
        if ($node)
            return $node;

        $node = new Node();
        $node->uid = $submissionNode->uid();
        $node->name = $submissionNode->name();

        $this->event->nodes()->save($node);

        return $node;
    }

    private function syncDrops(Node $node, SubmissionNode $submissionNode)
    {
        $untouchedDropIds = $node->drops()->pluck('id');

        foreach ($submissionNode->drops() as $submissionDrop) {
            $drop = $node->drops()
                ->where('uid', '=', $submissionDrop->uid())
                ->where('quantity', '=', $submissionDrop->quantity())
                ->first();

            if (!$drop) {
                $drop = new Drop();
                $drop->uid = $submissionDrop->uid();
                $drop->quantity = $submissionDrop->quantity();
                $node->drops()->save($drop);
            }

            $untouchedDropIds = $untouchedDropIds->diff([$drop->id]);
        }

        $node->drops()->whereIn('id', $untouchedDropIds)->delete();
    }
}
