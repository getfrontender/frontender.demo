<?php

namespace Prototype\Model\Unsplash;

use Slim\Container;
use Frontender\Core\Model\AbstractModel;

class ImageModel extends AbstractModel
{
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->getState()
            ->insert('id', 'int', null, true);
    }

    public function fetch()
    {
        // Force it to an array, we need to access "private" data.
        $data = \Crew\Unsplash\Photo::find($this->getState()->id);
        $properties = ['id', 'created_at', 'updated_at', 'width', 'height', 'color', 'description', 'alt_description', 'urls'];
        $image = [];

        foreach ($properties as $property) {
            $image[$property] = $data->{$property};
        }

        $model = new ImageModel($this->container);
        $model->setState($this->getState()->getValues())
            ->setData($image);

        return [$model];
    }
}
