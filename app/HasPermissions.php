<?php

namespace App;

/**
 * Interface HasPermissions
 * @package App
 */
interface HasPermissions
{
    /**
     * @param $permission
     * @param bool $requireAll
     * @return mixed
     */
    public function can($permission, $requireAll = false);
}