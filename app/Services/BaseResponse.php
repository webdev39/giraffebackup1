<?php

namespace App\Services;

/**
 * Class BaseResponse
 *
 * @package App\Services
 */
class BaseResponse
{
    const SNAKE_CASE = 'snake_case';
    const CAMEL_CASE = 'camel_case';

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode(get_object_vars($this));
    }

    /**
     * @return array
     */
    public function getArray()
    {
        return get_object_vars($this);
    }

    /**
     * return response array with snake case keys
     *
     * @return array
     */
    public function getSnakeCaseArray()
    {
        return $this->recursiveChangeKey(get_object_vars($this), self::SNAKE_CASE);
    }

    /**
     * return response array with camel case keys
     *
     * @return array
     */
    public function getCamelCaseArray()
    {
        return $this->recursiveChangeKey(get_object_vars($this));
    }

    /**
     * recursive change the array keys
     *
     * @param array $array
     * @param string $function
     * @return array
     */
    protected function recursiveChangeKey(array $array, $function = self::CAMEL_CASE)
    {
        $changedArray = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $changedArray[$function($key)] = $this->recursiveChangeKey($value, $function);
            } else {
                $changedArray[$function($key)] = $value;
            }
        }
        return $changedArray;
    }
}