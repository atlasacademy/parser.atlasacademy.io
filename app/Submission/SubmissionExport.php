<?php

namespace App\Submission;

use App\Drop;
use App\Node;
use Ramsey\Uuid\Uuid;

class SubmissionExport
{

    private $drops = [];
    /**
     * @var Node
     */
    private $node;
    /**
     * @var string | null
     */
    private $submitter;
    /**
     * @var string
     */
    private $token;
    /**
     * @var string
     */
    private $type;

    public function __construct(Node $node, string $type, ?string $submitter)
    {
        $this->node = $node;
        $this->submitter = $submitter;
        $this->token = Uuid::uuid4()->toString();
        $this->type = $type;
    }

    public function addDrop(Drop $drop, int $count)
    {
        $key = $this->dropKey($drop);

        $this->drops[$key] = ($this->drops[$key] ?? 0) + $count;
    }

    public function submitter(): ?string
    {
        return $this->submitter;
    }

    public function toArray(): array
    {
        $drops = [];

        foreach ($this->node->drops as $drop) {
            if ($this->type === 'simple' && $drop->isSimpleDrop()) {
                $drops[] = [
                    "uid" => $drop->uid,
                    "quantity" => $drop->quantity,
                    "count" => 0,
                    "ignored" => true,
                ];

                continue;
            }

            $key = $this->dropKey($drop);
            $drops[] = [
                "uid" => $drop->uid,
                "quantity" => $drop->quantity,
                "count" => $this->drops[$key] ?? 0,
                "ignored" => false,
            ];
        }

        return [
            "event_uid" => $this->node->event->uid,
            "event_node_uid" => $this->node->uid,
            "submitter" => $this->submitter,
            "token" => $this->token,
            "drops" => $drops,
        ];
    }

    public function token(): string
    {
        return $this->token;
    }

    private function dropKey(Drop $drop): string
    {
        return "{$drop->uid}-{$drop->quantity}";
    }

}
