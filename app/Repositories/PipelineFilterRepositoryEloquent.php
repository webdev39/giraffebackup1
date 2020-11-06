<?php

namespace App\Repositories;

use App\Models\PipelineFilter;

/**
 * Class PipelineFilterRepositoryEloquent
 *
 * @package App\Repositories
 *
 * @author  LexXurio
 */
class PipelineFilterRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PipelineFilter::class;
    }

}