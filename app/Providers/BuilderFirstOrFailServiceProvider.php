<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider;

class BuilderFirstOrFailServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        Builder::macro("firstOrFail", function ($columns = ['*']) {
            if (! is_null($model = $this->first($columns))) {
                return $model;
            }

            throw (new ModelNotFoundException)->setModel(get_class($this->model));
        });
    }
}