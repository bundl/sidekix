<?php
/**
 * @author  brooke.bryan
 */

namespace Bundl\Sidekix;

use Cubex\Bundle\Bundle;
use Cubex\Cookie\Cookies;
use Cubex\Cookie\StandardCookie;
use Cubex\Events\EventManager;
use Cubex\Foundation\Container;

class SidekixBundl extends Bundle
{
  public function init($initialiser = null)
  {
    EventManager::listen(
      EventManager::CUBEX_PROJECT_PREPARE,
      [$this, "projectPrepare"]
    );
  }

  public function projectPrepare()
  {
    $version = Container::request()->getVariables("DIFFUSE_VERSION", null);
    if($version !== null)
    {
      $cookie = new StandardCookie(
        Container::config()->get("sidekix", null)->getStr("diffuse_cookie"),
        $version
      );
      Cookies::set($cookie);
    }
  }
}
