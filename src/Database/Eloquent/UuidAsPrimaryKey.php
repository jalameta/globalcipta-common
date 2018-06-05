<?php

namespace GlobalCipta\Common\Database\Eloquent;

use Webpatser\Uuid\Uuid;

/**
 * UUID as primary key in eloquent model.
 *
 * @author      veelasky <veelasky@gmail.com>
 */
trait UuidAsPrimaryKey
{
    /**
     * Generate UUID as primary key upon creating new record on eloquent model.
     *
     * @return void
     */
    public static function bootUuidAsPrimaryKey()
    {
        self::creating(function ($model) {
            if (empty($model->{$model->getKeyName})) {
                $model->{$model->getKeyName()} = Uuid::generate()->string;
            }
        });
    }
}
