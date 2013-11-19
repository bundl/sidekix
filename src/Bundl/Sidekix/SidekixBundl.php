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

  public function translate($text, $sourceLanguage, $targetLanguage)
  {
    $config       = Container::config()->get("sidekix", new Config());
    $translateApi = $config->getStr("translate_endpoint", null);
    if($translateApi !== null)
    {
      $postData = [
        'text'   => $text,
        'source' => $sourceLanguage,
        'target' => $targetLanguage
      ];

      $curlHandle = curl_init($translateApi);
      curl_setopt($curlHandle, CURLOPT_HEADER, false);
      curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curlHandle, CURLOPT_POST, 1);
      curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $postData);

      $response = curl_exec($curlHandle);
      curl_close($curlHandle);
      return $response;
    }
    else
    {
      throw new \Exception(
        "No Translate Endpoint specified in config file", 400
      );
    }
  }
}
