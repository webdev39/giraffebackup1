<?php

namespace App\Collections;

use Illuminate\Support\Collection;

class TaskCollection extends BaseCollection
{
    public function setFilterSortOrder(Collection $collection)
    {
        return $this->mutator(function(array $value) use ($collection)
        {
            if ($sortOrder = $collection->where('task_id', $value['id'])->first()) {
                $value['sort_order'] = $sortOrder->sort_order;
            }

            return $value;
        });
    }

    public function setSortOrder(Collection $collection)
    {
        return $this->mutator(function(array $value) use ($collection)
        {
            if ($sortOrder = $collection->get($value['id'])) {
                $items = [];

                foreach ($sortOrder as $item) {
                    $items[$item->type] = $item->order;
                }

                $value['sort_order'] = $items;
            }

            return $value;
        });
    }

    public function setCountAttributes(array $attributes)
    {
        array_walk($attributes, function(&$items, $key) {
            $items = $this->collectionToSingleLevelArray($items, 'task_id', 'total');
        });

        return $this->mutator(function(array $value) use ($attributes)
        {
            $value['count'] = [];

            foreach ($attributes as $name => $attribute) {
                $value['count'][$name] = $attribute[$value['id']] ?? 0;
            }

            return $value;
        });
    }

    /**
     * @param Collection $collection
     * @param string     $keyToKey
     * @param string     $keyToValue
     *
     * @return array
     */
    private function collectionToSingleLevelArray(Collection $collection, string $keyToKey, string $keyToValue)
    {
        return $this->array_map_assoc(function ($k, $v) use ($keyToKey, $keyToValue) {
            return [$v->$keyToKey, $v->$keyToValue];
        }, $collection->toArray());
    }

    /**
     * @param callable $f
     * @param array    $a
     *
     * @return array
     */
    private function array_map_assoc(callable $f, array $a)
    {
        return array_column(array_map($f, array_keys($a), $a), 1, 0);
    }
}