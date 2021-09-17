<?php

namespace Framework\Traits;

use Framework\Exceptions\ForbiddenException;
use Symfony\Component\HttpFoundation\Session\Session;

/**
* Trait Traits
* @author Erwin Pakpahan <erwinmaruli@live.com>
*
*/
trait Secureable
{
  /**
   * @throws \Framework\Exceptions\ForbiddenException
   * @throws \Framework\Exceptions\ContainerException
   * @throws \Framework\Exceptions\RouteNotFoundException
   * @throws \Framework\Exceptions\ContainerNotFoundException
   */
	public function authorize() : void
	{
    if (!app(Session::class)->has('username')) {
      throw new ForbiddenException;
    }
	}
}
