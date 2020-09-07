<?php

namespace App\Submission;

class SubmissionDrop
{

    /**
     * @var array
     */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function quantity(): int
    {
        return $this->data['quantity'];
    }

    public function uid(): string
    {
        return $this->data['uid'];
    }

}
