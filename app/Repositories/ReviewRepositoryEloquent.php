<?php

namespace App\Repositories;

use App\Models\Review;

class ReviewRepositoryEloquent extends BaseRepositoryEloquent
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Review::class;
    }

}