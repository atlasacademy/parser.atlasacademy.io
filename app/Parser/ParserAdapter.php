<?php

namespace App\Parser;

use App\Submission;
use File;
use GuzzleHttp\Client;

class ParserAdapter
{

    public function emptyOutput(Submission $submission)
    {
        File::delete($this->outputPath($submission));
        File::delete($this->jsonPath($submission));
    }

    public function getOutput(Submission $submission): ?string
    {
        if (!$this->hasOutput($submission))
            return null;

        return File::get($this->jsonPath($submission));
    }

    public function hasOutput(Submission $submission): bool
    {
        return File::exists($this->jsonPath($submission));
    }

    public function input(Submission $submission)
    {
        $client = new Client();
        $response = $client->request('GET', $submission->image, [
            'sink' => $this->stagePath($submission)
        ]);

        File::move($this->stagePath($submission), $this->inputPath($submission));
    }

    private function inputPath(Submission $submission): string
    {
        return "/parser/input/{$submission->id}";
    }

    private function jsonPath(Submission $submission): string
    {
        return "/parser/output/{$submission->id}.json";
    }

    private function outputPath(Submission $submission): string
    {
        return "/parser/output/{$submission->id}";
    }

    private function stagePath(Submission $submission): string
    {
        return "/parser/stage/{$submission->id}";
    }

}
