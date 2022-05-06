<?php

namespace Tabus\Responses;

use Tabus\Support\Collection;

class Response extends Collection
{
    /**
     * @param array|stdClass $items
     */
    public function __construct($items = [])
    {
        parent::__construct($items);

        // стандартизируем ответ
        $this->items = $this->standartize($this->items);

        // TODO: рекурсивная функция, по всем массивам
        if (isset($this->items['results'])) {
            $this->items['results'] = $this->standartize($this->items['results']);
        }
    }

    /**
     * Удаляет мусорные символы, стандартизирует массив.
     *
     * @param array $items
     * @return array
     */
    public function standartize(array $items): array
    {
        foreach ($items as &$item) {
            $values = [
                '-','–','—', [], '',
            ];
            if (in_array($item, $values)) {
                $item = null;
            }
        }

        return $items;
    }

    /**
     * @param int $size
     * @return void
     */
    public function makePagination(int $size): void
    {
        if (!isset($this->items['total']) || (!$this->items['next_page'] & !$this->items['prev_page'])) {
            return;
        }

        $this->items['pagination']['perPage'] = count($this->items['results']);
        $this->items['pagination']['itemsCount'] = $this->items['total'];
        $this->items['pagination']['pagesCount'] = (int) ceil($this->items['pagination']['itemsCount'] / $this->items['pagination']['perPage']);

        if ($this->items['next_page']) {
            $arrUrl = parse_url($this->items['next_page']);
            parse_str($arrUrl['query'], $arrQuery);
            $this->items['pagination']['currentPage'] = $arrQuery['page'] - 1;
            $basePageUrl = str_replace("page={$arrQuery['page']}", 'page={page}', $this->items['next_page']);
        } else {
            $arrUrl = parse_url($this->items['prev_page']);
            parse_str($arrUrl['query'], $arrQuery);
            $this->items['pagination']['currentPage'] = $arrQuery['page'] + 1;
            $basePageUrl = str_replace("page={$arrQuery['page']}", 'page={page}', $this->items['prev_page']);
        }

        if ($this->items['pagination']['currentPage'] < $this->items['pagination']['pagesCount']) {
            $this->items['pagination']['nextPage'] = $this->items['pagination']['currentPage'] + 1;
            $this->items['pagination']['nextPageUrl'] = str_replace('{page}', $this->items['pagination']['nextPage'], $basePageUrl);
            $this->items['pagination']['hasNextPage'] = true;
        } else {
            $this->items['pagination']['nextPage'] = null;
            $this->items['pagination']['hasNextPage'] = false;
        }

        if ($this->items['pagination']['currentPage'] > 0) {
            $this->items['pagination']['prevPage'] = $this->items['pagination']['currentPage'] - 1;
            $this->items['pagination']['nextPageUrl'] = str_replace('{page}', $this->items['pagination']['prevPage'], $basePageUrl);
            $this->items['pagination']['hasPrevPage'] = true;
        } else {
            $this->items['pagination']['prevPage'] = null;
            $this->items['pagination']['hasPrevPage'] = false;
        }

        $pages = range($this->items['pagination']['currentPage'] - $size, $this->items['pagination']['currentPage'] + $size);

        $firstPage = $pages[0];
        $lastPage = end($pages);

        if ($firstPage - $size > 0) {
            array_unshift($pages, '...');
        }
        array_unshift($pages, 0);

        if ($lastPage + $size < $this->items['pagination']['pagesCount']) {
            $pages[] = '...';
        }
        $pages[] = $this->items['pagination']['pagesCount'];

        $this->items['pagination']['pages'] = (new Collection($pages))
            ->filter(fn ($item) => $item >= 0 && $item <= $this->items['pagination']['pagesCount'] || $item === '...')
            ->mapWithKeys(fn ($item) => $item === '...' ? ['_' . rand(1000,9999) => $item] : [$item => str_replace('{page}', $item, $basePageUrl)])
            ->toArray();
    }

    /**
     * @return bool
     */
    public function hasNextPage(): bool
    {
        return isset($this->items['next_page']) && $this->items['next_page'] !== null;
    }

    /**
     * @return bool
     */
    public function hasPrevPage(): bool
    {
        return isset($this->items['prev_page']) && $this->items['prev_page'] !== null;
    }

    /**
     * @return self|null
     */
    public function getNextPage(): self|null
    {
        return $this->hasNextPage() ? $this->client->raw($this->items['next_page']) : null;
    }

    /**
     * @return self|null
     */
    public function getPrevPage(): self|null
    {
        return $this->hasPrevPage() ? $this->client->raw($this->items['prev_page']) : null;
    }
}
