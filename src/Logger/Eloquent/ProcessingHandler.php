<?php

namespace GlobalCipta\Common\Logger\Eloquent;

use Monolog\Handler\AbstractProcessingHandler;

/**
 * Eloquent Log Handler
 *
 * @author      veelasky <veelasky@gmail.com>
 */
class ProcessingHandler extends AbstractProcessingHandler
{
    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     * @return void
     */
    protected function write(array $record)
    {
       $record = new LogModel();
       $record->fill([
            'env'     => $record['channel'],
            'message' => $record['message'],
            'level'   => $record['level_name'],
            'context' => $record['context'],
            'extra'   => $record['extra']
        ])->save();
    }
}
