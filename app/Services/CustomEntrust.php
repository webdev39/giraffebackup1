<?php
/**
 * Created by PhpStorm.
 * User: nikolaygolub
 * Date: 17.11.2017
 * Time: 14:16
 */

namespace App\Services;


use App\Models\UserTenant;
use Zizaco\Entrust\Entrust;

class CustomEntrust extends Entrust
{
    /**
     * Get the currently authenticated user or null.
     *
     * @return UserTenant|null
     */
    public function user()
    {
        return $this->app->auth->user()->getChosenTenant();
    }

}