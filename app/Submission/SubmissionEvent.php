<?php

namespace App\Submission;

class SubmissionEvent
{

    /**
     * @var array
     */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function name(): string
    {
        return $this->data['name'];
    }

    /**
     * @return SubmissionNode[]
     */
    public function nodes(): array
    {
        return array_map(function (array $data) {
            $nodeUid = $data['uid'];
            $dropData = array_filter($this->data['node_drops'], function (array $dropData) use ($nodeUid) {
                return $dropData['event_node_uid'] === $nodeUid;
            });

            return new SubmissionNode($data, $dropData);
        }, $this->data['nodes']);
    }

    public function uid(): string
    {
        return $this->data["uid"];
    }

}
