<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel
 *
 * @package App\Models
 */
abstract class BaseModel extends Model
{
    /**
     * Default log name for this model
     *
     * @var string
     */
    public $logName;

    /**
     * Logging only the changed attributes
     *
     * @var array
     */
    public $logAttributes = [];

    /**
     * List of attributes when changing which notification of subscribers
     *
     * @var array
     */
    public $notifyAttributes = [];

    /**
     * Generating a key for a hash
     *
     * @param array  $params
     * @param string $key
     *
     * @return string
     */
    public function cacheKey(array $params, string $key ) : string
    {
        $query = http_build_query($params, '', '/');

        return sprintf("%s/%s-%s/%s:%s", $this->getTable(), $this->getKey(), $this->updated_at->timestamp, $query, $key);
    }

    /**
     * Convert stdClass to Model
     *
     * @param \stdClass $std
     *
     * @return static
     */
    public static function createFromStd(\stdClass $std)
    {
        $instance = new static;

        foreach ((array) $std as $attribute => $value) {
            $instance->{$attribute} = $value;
        }

        return $instance;
    }
}
