<?php

namespace App\Repositories;

use App\Models\Repeat;

/**
 * Class RepeatRepositoryEloquent
 *
 * @package App\Repositories
 * @property Budget $model
 */
class RepeatRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Repeat::class;
    }
}
