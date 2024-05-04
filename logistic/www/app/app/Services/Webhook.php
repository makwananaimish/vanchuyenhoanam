<?php

namespace App\Services;

use GuzzleHttp\Client;

class Webhook
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://partner.shopf1.net',
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function post($uri, $json)
    {
        return $this->client->post($uri, [
            'json' => $json,
        ]);
    }

    public function postToWebhookDotSite($json)
    {
        return $this->post('/hoa-nam', $json);
    }

    public function test($json)
    {
        // https://webhook.site/243b1273-9133-4f40-a8d9-075f9af299de

        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        return $client->post('https://webhook.site/243b1273-9133-4f40-a8d9-075f9af299de', [
            'json' => $json,
        ]);
    }
}
