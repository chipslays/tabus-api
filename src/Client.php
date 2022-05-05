<?php

namespace Tabus;

use Tabus\Responses\JsonResponse;
use Tabus\Responses\XmlResponse;
use Tabus\Constants\Format;
use Tabus\Support\Cache;
use CurlHandle;

class Client
{
    protected CurlHandle $ch;

    protected ?Cache $cache = null;

    public array $defaultParameters = [
        'limit' => 20,
        'page' => 0,
    ];

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
        public string $format = Format::JSON,
    ) {
        $this->domain = rtrim($domain, '\\/');
        $this->ch = curl_init();
    }

    /**
     * Запрос к API с методом и массивом параметров.
     *
     * @param string $method
     * @param array $parameters
     * @return JsonResponse|XmlResponse
     *
     * @see https://tabus.me/docs#api Документация
     */
    public function api(string $method, array $parameters = []): JsonResponse|XmlResponse
    {
        return $this->raw($this->domain . '/' . $method . '?' . http_build_query(
            array_merge([
                'token' => $this->token,
                'format' => $this->format,
            ], $this->defaultParameters, $parameters)
        ));
    }

    /**
     * Запрос к API в виде готовой ссылки.
     *
     * @param string $url
     * @return JsonResponse|XmlResponse
     *
     * @see https://tabus.me/docs#api Документация
     */
    public function raw(string $url): JsonResponse|XmlResponse
    {
        $url = $this->cache ? $this->modifyDomain($url) : $url;

        $cacheKey = md5($url);

        if ($this->cache?->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($this->ch);

        switch ($this->format) {
            case Format::JSON:
                $response = new JsonResponse($this, json_decode($output, true));
                break;

            case Format::XML:
                $data = json_decode(json_encode(simplexml_load_string($output)), true);
                $response = new XmlResponse($this, $data['response'] ?? $data);
                break;
        }

        $this->cache?->put($cacheKey, $response);

        return $response;
    }

    /**
     * Задать параметры кэширования запросов.
     *
     * @param string $path Путь до директории где будут лежать файлы кэша.
     * @param int $ttl Длительность кэширования в секундах.
     * @return void
     */
    public function cache(string $path, int $ttl = Cache::DEFAULT_TTL): void
    {
        $this->cache = new Cache($this, $path, $ttl);
    }

    /**
     * @param string $url
     * @return string
     */
    protected function modifyDomain(string $url): string
    {
        preg_match('/api\d+/', $this->domain, $matches);

        return preg_replace('/api\d+/', $matches[0], $url, 1);
    }
}
