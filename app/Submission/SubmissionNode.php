<?php

namespace App\Submission;

class SubmissionNode
{

    /**
     * @var array
     */
    private $data;
    /**
     * @var array
     */
    private $dropData;

    public function __construct(array $data, array $dropData)
    {
        $this->data = $data;
        $this->dropData = $dropData;
    }

    /**
     * @return SubmissionDrop[]
     */
    public function drops(): array
    {
        return array_map(function (array $data) {
            return new SubmissionDrop($data);
        }, $this->dropData);
    }

    public function name(): string
    {
        return $this->data['name'];
    }

    public function uid(): string
    {
        return $this->data['uid'];
    }

}
