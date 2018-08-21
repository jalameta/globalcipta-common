<?php

namespace GlobalCipta\Common\Logger\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Log Eloquent Model
 *
 * @author      veelasky <veelasky@gmail.com>
 */
class LogModel extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'system_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'env',
        'message',
        'level',
        'context',
        'extra'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'context' => 'array',
        'extra' => 'array'
    ];
}
