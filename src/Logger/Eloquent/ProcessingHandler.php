<?php

namespace GlobalCipta\Common\Logger\Eloquent;

use Monolog\Handler\AbstractProcessingHandler;

/**
 * Eloquent Log Handler.
 *
 * @author      veelasky <veelasky@gmail.com>
 */
class ProcessingHandler extends AbstractProcessingHandler
{
    /**
     * Writes the record down to the log of the implementing handler.
     *
     * @param  array $record
     * @return void
     */
    protected function write(array $record)
    {
        $new = new LogModel();

        $new->fill([
            'env' => $record['channel'],
            'message' => $record['message'],
            'level' => $record['level_name'],
            'context' => $record['context'],
            'extra' => array_merge($record['extra'], $this->getExtraAttributes()),
        ])->save();
    }

    /**
     * Get Provisional Extra Attribute.
     *
     * @return array
     */
    protected function getExtraAttributes()
    {
        $extra = [];

        if (! app()->runningInConsole()) {
            $extra['serve'] = request()->server('SERVER_ADDR');
            $extra['host'] = request()->getHost();
            $extra['uri'] = request()->getPathInfo();
            $extra['request'] = request()->all();
        }

        return $extra;
    }
}
