<?php

namespace Tabus;

use CurlHandle;
use Chipslays\Collection\Collection;

class Client
{
    protected CurlHandle $ch;

    /**
     * Constructor.
     *
     * @param string $token
     * @param string $domain
     * @param string $format
     */
    public function __construct(
        protected string $token,
        protected string $domain,
        protected string $format = 'json',
    ) {
        $this->domain = rtrim($domain, '\\/');
        $this->ch = curl_init();
    }

    /**
     * Чистый запрос к API.
     *
     * @param string $method
     * @param array $parameters
     * @return string|Collection
     */
    public function api(string $method, array $parameters = []): string|Collection
    {
        $url = $this->domain . '/' . $method . '?' . http_build_query(
            array_merge(['token' => $this->token], $parameters)
        );

        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($this->ch);

        switch ($this->format) {
            case 'json':
                return new Collection(json_decode($response, true));
            default:
                return $response;
        }
    }
}
