<?php

namespace m4rku5\Clockify;

use RuntimeException;

class Clockify
{
    /** @var string $baseurl */
    protected string $baseurl;
    /** @var string $apikey */
    protected string $apikey;

    public function __construct(string $baseurl, string $apikey)
    {
        $this->baseurl = $baseurl;
        $this->apikey = $apikey;
    }

    private function _curl(string $url, array $options = []): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, array_replace_recursive([
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => [
                "X-Api-Key: " . $this->apikey,
                "Content-Type: application/json; charset=utf-8",
            ],
        ], $options));
        $exec = curl_exec($ch);
        curl_close($ch);

        return json_decode($exec, true);
    }

    public function GET(string $endpoint): array
    {
        return $this->_curl($this->baseurl . '/' . $endpoint, [CURLOPT_HTTPGET => true]);
    }

    public function PUT()
    {
        throw new RuntimeException('not yet implemented');
    }

    public function POST(string $endpoint, array $data): array
    {
        return $this->_curl($this->baseurl . '/' . $endpoint, [
            CURLOPT_POST       => true,
            CURLOPT_POSTFIELDS => $data,
        ]);
    }

    public function user()
    {
        return $this->GET('user');
    }

    /**
     * Get all time entries for today for active workspace of current user
     *
     * @return array
     */
    public function today()
    {
        $user = $this->user();

        return $this->GET('workspaces/' . $user['activeWorkspace'] . '/user/' . $user['id'] . '/time-entries?start=' . strftime('%FT00:00:00.000Z'));
    }
}
