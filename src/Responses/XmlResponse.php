<?php

namespace Tabus\Responses;

use Tabus\Client;
use Tabus\Support\Collection;

class XmlResponse extends Response
{
    /**
     * @param array|stdClass $items
     */
    public function __construct(protected Client &$client, $items = [])
    {
        parent::__construct($items);

        if (isset($this->items['results']['item'])) {
            $this->items['results'] = new Collection($this->items['results']['item']);
        } elseif (isset($this->items['results'])) {
            // если данные взяты из кэша
            $this->items['results'] = new Collection($this->items['results']);
        }

        if (isset($this->items['items']['item'])) {
            $this->items['results'] = new Collection($this->items['items']['item']);
        }

        $this->paginate($client->getPaginate());
    }
}
