<?php

namespace Frontender\Platform\Template\Filter;

class ArrayFilter extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_Filter('key_value_join', [$this, 'keyValueJoin'])
        ];
    }

    public function keyValueJoin(array $array = [], string $separator = '='): array {
        $newArray = [];

        foreach($array as $key => $value) {
            $newArray[] = $key . $separator . $value;
        }

        return $newArray;
    }
}