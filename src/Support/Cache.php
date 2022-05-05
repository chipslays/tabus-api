<?php

namespace Tabus\Support;

use Tabus\Client;
use Tabus\Constants\Format;
use Tabus\Responses\XmlResponse;
use Tabus\Responses\JsonResponse;

class Cache
{
    public const DEFAULT_TTL = 300;

    public string $path;

    public int $ttl;

    /**
     * Constructor.
     *
     * @param Client $client
     * @param string $path Путь до директории где будут лежать файлы кэша.
     * @param int $ttl Длительность кэширования в секундах.
     */
    public function __construct(
        protected Client &$client,
        string $path,
        int $ttl = self::DEFAULT_TTL
    ) {
        $this->path = rtrim($path, '/\\');
        $this->ttl = abs($ttl);
    }

    /**
     * @param string $key
     * @param JsonResponse|XmlResponse $response
     * @return void
     */
    public function put(string $key, JsonResponse|XmlResponse $response): void
    {
        $data = $response->toArray();

        if (isset($data['results']) && $data['results'] instanceof Collection) {
            $data['results'] = $data['results']->toArray();
        }

        file_put_contents($this->makePathTo($key), serialize($data));
    }

    /**
     * @param string $file
     * @return bool
     */
    public function expired(string $file): bool
    {
        return filemtime($file) + $this->ttl < time();
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        $file = $this->makePathTo($key);

        if (file_exists($file) && !$this->expired($file)) {
            return true;
        } else {
            file_exists($file) ? unlink($file) : null;
            return false;
        }
    }

    /**
     * @param string $key
     * @return JsonResponse|XmlResponse
     */
    public function get(string $key): JsonResponse|XmlResponse
    {
        $data = unserialize(file_get_contents($this->makePathTo($key)));

        switch ($this->client->format) {
            case Format::JSON:
                return new JsonResponse($this->client, $data);
                break;

            case Format::XML:
                return new XmlResponse($this->client, $data);
                break;
        }
    }

    /**
     * @param string $key
     * @return string
     */
    protected function makePathTo(string $key): string
    {
        return $this->path . '/' . $key . '.cache';
    }
}
