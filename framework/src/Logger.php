<?php

namespace Framework;

use Framework\LoggerInterface;


/**
 * Class Logger
 * @author Erwin Pakpahan <erwinmaruli@live.com>
 */
class Logger implements LoggerInterface
{
    public function __construct(
        private string $fileName = "error.log"
    ) { }

    /**
     * @return int|false
     */
    public function log(string $message, array $data) : int|false
    {
        foreach ($data as $key => $val) {
            $message = str_replace("%{$key}%", $val, $message);
        }

        $message .= PHP_EOL;
        return file_put_contents($this->fileName, $message, FILE_APPEND);
    }
}
