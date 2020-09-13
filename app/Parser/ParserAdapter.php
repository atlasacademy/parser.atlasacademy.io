<?php

namespace App\Parser;

use App\Submission;
use File;
use GuzzleHttp\Client;

class ParserAdapter
{
    public const INPUT_DIRECTORY = '/parser/input';
    public const OUTPUT_DIRECTORY = '/parser/output';
    public const STAGE_DIRECTORY = '/parser/stage';
    public const TEMPLATE_DIRECTORY = '/parser/item';

    public function deleteTemplate(string $name)
    {
        [$path, $folder] = $this->findTemplate($name);

        File::delete($path);
    }

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

    public function getTemplateData(string $name): ?string
    {
        [$path, $folder] = $this->findTemplate($name);

        if ($path === null)
            return null;

        return base64_encode(File::get($path));
    }

    public function hasOutput(Submission $submission): bool
    {
        return File::exists($this->jsonPath($submission));
    }

    public function hasUnknownCode(string $code): bool
    {
        [$path, $folder] = $this->findTemplate($code);

        return boolval($path);
    }

    public function input(Submission $submission)
    {
        $client = new Client();
        $response = $client->request('GET', $submission->image, [
            'sink' => $this->stagePath($submission)
        ]);

        File::move($this->stagePath($submission), $this->inputPath($submission));
    }

    public function renameUnknownCode($from, $to)
    {
        [$fromPath, $folder] = $this->findTemplate($from);

        $toPath = $this->templatePath($folder, $to);
        File::move($fromPath, $toPath);
    }

    private function findTemplate(string $code): array
    {

        $dirs = File::directories("/parser/item");

        foreach ($dirs as $dir) {
            $folder = File::basename($dir);
            $path = $this->templatePath($folder, $code);
            if (File::exists($path))
                return [$path, $folder];
        }

        return [null, null];
    }

    private function inputPath(Submission $submission): string
    {
        return self::INPUT_DIRECTORY . "/{$submission->id}";
    }

    private function jsonPath(Submission $submission): string
    {
        return self::OUTPUT_DIRECTORY . "/{$submission->id}.json";
    }

    private function outputPath(Submission $submission): string
    {
        return self::OUTPUT_DIRECTORY . "/{$submission->id}";
    }

    private function stagePath(Submission $submission): string
    {
        return self::STAGE_DIRECTORY . "/{$submission->id}";
    }

    private function templatePath(string $directory, string $template): string
    {
        return self::TEMPLATE_DIRECTORY . "/{$directory}/{$template}.png";
    }

}
