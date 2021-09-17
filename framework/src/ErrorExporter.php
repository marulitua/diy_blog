<?php

namespace Framework;

use Throwable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

/**
 * Class ErrorExporter
 * @author Erwin Pakpahan <erwinmaruli@live.com>
 */
class ErrorExporter
{
    public function export(Throwable $e) : string
    {
        if (isProduction()) {
            $errorMessage = "Internal Server Error";
        } else {
            $cloner = new VarCloner();
            $dumper = new HtmlDumper();
            $errorMessage = $dumper->dump($cloner->cloneVar($e), true);
        }

        return $errorMessage;
    }
}
