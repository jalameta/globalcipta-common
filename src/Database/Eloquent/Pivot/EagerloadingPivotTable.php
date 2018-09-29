<?php

namespace GlobalCipta\Common\Database\Eloquent\Pivot;

/**
 * EagerLoadingPivotTable
 *
 * @author      veelasky <veelasky@gmail.com>
 */
trait EagerloadingPivotTable
{
    public function newEloquentBuilder($query)
    {
        return new EloquentBuilder($query);
    }
}
