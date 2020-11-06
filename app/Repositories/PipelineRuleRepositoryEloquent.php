<?php

namespace App\Repositories;

use App\Models\PipelineRule;

/**
 * Class PipelineRuleRepositoryEloquent
 *
 * @package App\Repositories
 *
 * @author  LexXurio
 */
class PipelineRuleRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PipelineRule::class;
    }

}