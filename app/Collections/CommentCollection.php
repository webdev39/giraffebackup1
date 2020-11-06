<?php

namespace App\Collections;

use Illuminate\Support\Collection;

/**
 * Class CommentCollection
 *
 * @package App\Collections
 */
class CommentCollection extends BaseCollection
{
    /**
     * @param Collection $collection
     *
     * @return CommentCollection
     */
    public function setDescendants(Collection $collection)
    {
        return $this->mutator(function(array $value) use ($collection)
        {
            if ($comment = $collection->where('id', $value['id'])->first()) {
                $value['descendants'] = $comment->descendants;
            }

            return $value;
        });
    }
}
