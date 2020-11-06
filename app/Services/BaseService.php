<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class BaseService
{
    /**
     * @param float $val
     * @param int   $precision
     *
     * @return float
     */
    public static function roundFloatUp(float $val, int $precision = 2) : float
    {
        if ($val === round($val, $precision)) {
            return $val;
        }

        return $val > 0 ? round($val, 2) + 1 / pow(10, $precision) : 0 ;
    }

    /**
     * @param       $items
     * @param null  $page
     * @param int   $perPage
     * @param array $options
     *
     * @return LengthAwarePaginator
     */
    public function paginate($items, $page = null, $perPage = 30, $options = [])
    {
        $page  = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}