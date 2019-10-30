<?php

namespace Frontender\Platform\Model\Unsplash\Image;

use Slim\Container;
use Frontender\Platform\Model\Unsplash\ImageModel;
use Frontender\Platform\Model\Unsplash\AbstractModel;

class SearchModel extends AbstractModel
{
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->getState()
            ->insert('q')
            ->insert('orientation')
            ->insert('limit', 20)
            ->insert('skip')
            ->insert('id');
    }

    public function fetch()
    {
        $state = $this->getState()->getValues();
        $page = 1;
        $orientation = null;

        if(isset($state['id'])) {
            // Return the image model.
            $model = new ImageModel($this->container);
            $model->setState([
                'id' => $state['id']
            ]);

            return $model->fetch();
        }

        if (isset($state['skip']) && !empty($state['skip'])) {
            $page = ($state['skip'] / $state['limit']) + $page;
        }

        if (isset($state['q']) && !empty($state['q'])) {
            if(isset($state['orientation']) && !empty($state['orientation'])) {
                $orientation = $state['orientation'];
            }

            $data = \Crew\Unsplash\Search::photos($state['q'], $page, $state['limit'], $orientation);
            $data = $data->getResults();
        } else {
            $data = \Crew\Unsplash\Photo::all($page, $state['limit']);
            $images = $data->getArrayCopy();

            $data = array_map(function ($image) {
                return ImageModel::getImageArray($image);
            }, $images);
        }
        $container = $this->container;

        return array_map(function ($image) use ($container) {
            $model = new ImageModel($container);
            $model->setState([
                'id' => $image['id']
            ])->setData($image);

            return $model;
        }, $data);
    }

    public function getTotal()
    {
        $state = $this->getState()->getValues();

        if (isset($state['q']) && !empty($state['q'])) {
            $data = \Crew\Unsplash\Search::photos($state['q'], 1, $state['limit']);
            $total = $data->getTotal();
        } else {
            $data = \Crew\Unsplash\Photo::all(1, $state['limit']);
            $total = $data->totalObjects();
        }

        return $total;
    }
}
