<?php

namespace Prototype\Model\Unsplash\Image;

use Slim\Container;
use Prototype\Model\Unsplash\ImageModel;
use Frontender\Core\Model\AbstractModel;

class SearchModel extends AbstractModel
{
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->getState()
            ->insert('q', 'string')
            ->insert('limit', 'int', 20)
            ->insert('offset', 'int');
    }

    public function fetch()
    {
        $state = $this->getState()->getValues();
        $page = 1;

        if (isset($state['offset']) && !empty($state['offset'])) {
            $page = ($state['offset'] / $state['limit']) + $page;
        }

        $data = \Crew\Unsplash\Search::photos($state['q'], $page, $state['limit']);
        $container = $this->container;

        return array_map(function ($image) use ($container) {
            $model = new ImageModel($container);
            $model->setState([
                'id' => $image['id']
            ])->setData($image);

            return $model;
        }, $data->getResults());
    }

    public function getTotal()
    {
        $state = $this->getState()->getValues();
        $page = 1;

        if (isset($state['offset']) && !empty($state['offset'])) {
            $page = ($state['offset'] / $state['limit']) + $page;
        }

        $data = \Crew\Unsplash\Search::photos($state['q'], $page, $state['limit']);
        return $data->getTotal();
    }
}
