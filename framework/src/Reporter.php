<?php

namespace Framework;

use Throwable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

/**
 * Class Reporter
 * @author Erwin Pakpahan <erwinmaruli@live.com>
 */
class Reporter
{
    public function report(Throwable $e) {
        $cloner = app(VarCloner::class);
        $dumper = app(CliDumper::class);
        $request = Request::createFromGlobals();

        app('logger')->log(
            "%timestamp% %method% %uri% %ip% \n%message%",
            [
                "message" => $dumper->dump($cloner->cloneVar($e), true),
                "timestamp" => date('d.m.Y H:i:s'),
                "uri" => $request->getPathInfo(),
                "method" => $request->getMethod(),
                "ip" => $request->getClientIp(),
            ]
        );
    }
}
