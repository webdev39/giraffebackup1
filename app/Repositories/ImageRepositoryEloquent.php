<?php

namespace App\Repositories;

use App\Models\Image;

class ImageRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Image::class;
    }
}
