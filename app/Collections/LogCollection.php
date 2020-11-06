<?php

namespace App\Collections;

class LogCollection extends BaseCollection
{
    /**
     * @param UserCollection $collection
     *
     * @return $this
     */
    public function setAttachments(UserCollection $collection)
    {
        return $this->mutator(function(array $value) use ($collection)
        {
            if ($attachments = $collection->where('id', $value['user_id'])->first()) {
                $value['attachments'] = $attachments;
            }

            return $value;
        });
    }
}