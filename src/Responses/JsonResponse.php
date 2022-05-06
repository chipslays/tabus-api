<?php

namespace Tabus\Responses;

use Tabus\Client;
use Tabus\Support\Collection;

class JsonResponse extends Response
{
    /**
     * @param array|stdClass $items
     */
    public function __construct(protected Client &$client, $items = [])
    {
        parent::__construct($items);

        if (isset($this->items['results'])) {
            $this->items['results'] = new Collection($this->items['results']);
        }

        if (isset($this->items['items'])) {
            $this->items = $this->items['items'];
        }

        $this->paginate($client->getPaginate());
    }

    /**
     * @param int $size
     * @return void
     */
    public function paginate(int $size): void
    {
        $this->makePagination($size);
    }
}
