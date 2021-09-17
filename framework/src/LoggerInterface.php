<?php

namespace Framework;

/**
 * Interface LoggerInterface
 * @author Erwin Pakpahan <erwinmaruli@live.com>
 */
interface LoggerInterface
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function log(string $message, array $data) : int|false;
}
