<?php

namespace Pebble\Teams;

/**
 * Teams connector
 */
class TeamsConnector
{
    private string $webhookUrl;

    public function __construct(string $webhookUrl)
    {
        $this->webhookUrl = $webhookUrl;
    }

    /**
     * Sends card message as POST request
     *
     * @param  CardInterface $card
     * @throws Exception
     */
    public function send(CardInterface $card)
    {
        $this->sendRaw($card->getMessage());
    }

    /**
     * Sends card message as POST request
     *
     * @param  array $raw
     * @throws Exception
     */
    public function sendRaw(array $raw)
    {
        $this->sendJson(json_encode($raw));
    }

    /**
     * Sends card message as POST request
     *
     * @param  string $json
     * @throws Exception
     */
    public function sendJson(string $json)
    {
        $ch = curl_init($this->webhookUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json)
        ]);

        $result = curl_exec($ch);

        if (curl_error($ch)) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }

        if ($result !== "1") {
            throw new Exception('Error response: ' . $result);
        }
    }
}
