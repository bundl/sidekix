<?php
/**
 * @author  brooke.bryan
 */

namespace Bundl\Sidekix;

use Cubex\Bundle\Bundle;
use Cubex\Cookie\Cookies;
use Cubex\Cookie\StandardCookie;
use Cubex\Events\EventManager;
use Cubex\Facade\Redirect;
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
    $request  = Container::request();
    if($request === null)
    {
      return;
    }

    $version = $request->getVariables("DIFFUSE_VERSION", null);
    $secKey  = $request->getVariables("SEC", null);
    if($version !== null && $secKey === $security)
    {
      $cookie = new StandardCookie(
        $config->getStr("diffuse_cookie", "CUBEX_VERSION"), $version
      );
      Cookies::set($cookie);
      $redirect = $config->getStr("diffuse_cookie_redirect", null);
      if($redirect)
      {
        Redirect::to($redirect)->now();
      }
    }
  }

  public static function getDiffuseVersionInfo()
  {
    $info        = [];
    $versionFile = build_path(CUBEX_PROJECT_ROOT, 'DIFFUSE.VERSION');
    if(file_exists($versionFile))
    {
      $data = file($versionFile);
      foreach($data as $line)
      {
        $line = trim($line);
        if(starts_with($line, '== Change Log ==', true))
        {
          break;
        }
        else if(!empty($line))
        {
          list($key, $value) = exploded(':', $line, ['unknown', 'unknown'], 2);
          $info[$key] = $value;
        }
      }
    }

    return $info;
  }
}

