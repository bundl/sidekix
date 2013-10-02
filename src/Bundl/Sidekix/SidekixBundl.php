<?php
/**
 * @author  brooke.bryan
 */

namespace Bundl\Sidekix;

use Cubex\Bundle\Bundle;
use Cubex\Cookie\Cookies;
use Cubex\Cookie\StandardCookie;
use Cubex\Events\EventManager;
use Cubex\Foundation\Config\Config;
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
    $config   = Container::config()->get("sidekix", new Config());
    $security = $config->getStr("security_key", null);
    $version  = Container::request()->getVariables("DIFFUSE_VERSION", null);
    $secKey   = Container::request()->getVariables("SEC", null);
    if($version !== null && $secKey === $security)
    {
      $cookie = new StandardCookie(
        $config->getStr("diffuse_cookie", "CUBEX_VERSION"), $version
      );
      Cookies::set($cookie);
    }
  }
}
