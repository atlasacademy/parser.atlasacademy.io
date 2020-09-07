<?php

namespace App\Submission;

use GuzzleHttp\Client;

class SubmissionApi
{

    private const HOST = "https://submissions.atlasacademy.io";

    /**
     * @var Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            "base_uri" => self::HOST
        ]);
    }

    public function event(string $uid): ?SubmissionEvent
    {
        $response = $this->client->get("/event/{$uid}");
        if ($response->getStatusCode() !== 200)
            return null;

        $body = $response->getBody()->getContents();

        return new SubmissionEvent(json_decode($body, true));
    }

}
