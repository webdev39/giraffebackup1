<?php

namespace App\Collections;

use Illuminate\Support\Collection;

abstract class BaseCollection extends Collection
{
    /**
     * The accessors to append to the collections array form.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * BaseCollection constructor.
     *
     * @param array $items
     */
    public function __construct($items = [])
    {
        parent::__construct($items);
    }

    /**
     * @param array $attributes
     * @param bool  $single
     *
     * @return BaseCollection
     */
    public function setAttributes(array $attributes, bool $single = false)
    {
        return $this->mutator(function(array $value) use ($attributes, $single)
        {
            foreach ($attributes as $name => $attribute) {
                $value[$name] = self::getElement($attribute, $value['id']);

                if ($single) {
                    $value[$name] = self::getElement($value[$name]);
                } else {
                    $value[$name] = self::toCollection($value[$name])->values();
                }
            }

            return $value;
        });
    }

    /**
     * @param UserCollection $collection
     *
     * @return $this
     */
    public function setUser(UserCollection $collection)
    {
        return $this->mutator(function(array $value) use ($collection)
        {
            if ($user = $collection->where('id', $value['user_id'])->first()) {
                $value['user'] = $user;
            }

            return $value;
        });
    }

    /**
     * Get the collection of items as a plain array.
     *
     * @return array
     */
    public function toArray()
    {
        $this->setAppends();

        return parent::toArray();
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    protected function forgetAttributes(array $attributes)
    {
        return $this->mutator(function(array $value) use ($attributes)
        {
            foreach ($attributes as $attribute) {
                unset($value[$attribute]);
            }

            return $value;
        });
    }

    /**
     * Adding additional fields before responses
     */
    protected function setAppends()
    {
        foreach ($this->appends as $append) {
            $functionName = 'get'.ucfirst($append).'Attribute';

            if (method_exists($this, $functionName)) {
                $this->mutator(function(array $value) use ($functionName, $append)
                {
                    $value[$append] = $this->$functionName($value);

                    return $value;
                });
            }
        }
    }

    /**
     * Set count the number of items in data
     *
     * @param        $data
     * @param string $name
     */
    protected static function setCountAttribute(&$data, string $name)
    {
        $fieldName = str_singular($name) . 'Count';

        $data[$fieldName] = self::getCountItems($data[$name]);
    }

    /**
     * @param $items
     *
     * @return int
     */
    protected static function getCountItems($items)
    {
        switch (true) {
            case(is_array($items)):
                return count($items);
            case($items instanceof Collection):
                return $items->count();
            case($items):
                return 1;
            default:
                return 0;
        }
    }

    /**
     * @param     $item
     * @param int $id
     *
     * @return mixed
     */
    protected static function getElement($item, int $id = 0)
    {
        if ($item instanceof Collection) {
            return $item->get($id);
        }

        if (is_array($item)) {
            return $item[$id];
        }

        if ($item) {
            return $item;
        }

        return null;
    }

    /**
     * @param $item
     *
     * @return bool
     */
    protected static function isArrayOrCollection($item) : bool
    {
        return is_array($item) || $item instanceof Collection;
    }

    /**
     * @param $item
     *
     * @return Collection
     */
    protected static function toCollection($item) : Collection
    {
        if ($item instanceof Collection) {
            return $item;
        }

        if (is_array($item)) {
            return collect($item);
        }

        if ($item) {
            return collect([$item]);
        }

        return collect();
    }


    /**
     * Browse all the items of the collection with a callback to the function
     *
     * @param $callback
     *
     * @return $this
     */
    protected function mutator($callback)
    {
        array_walk($this->items, function(&$value) use ($callback) {
            $value = (object)call_user_func_array($callback, [(array) $value]);
        });

        return $this;
    }
}