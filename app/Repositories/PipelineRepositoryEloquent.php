<?php

namespace App\Repositories;

use App\Models\Pipeline;

/**
 * Class PipelineRepositoryEloquent
 *
 * @package App\Repositories
 *
 * @author  LexXurio
 */
class PipelineRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Pipeline::class;
    }
}
