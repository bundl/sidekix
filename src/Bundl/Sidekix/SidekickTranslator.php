<?php
/**
 * @author  brooke.bryan
 */

namespace Bundl\Sidekix;

use Cubex\Foundation\Config\Config;
use Cubex\Foundation\Config\ConfigTrait;
use Cubex\Foundation\Container;
use Cubex\I18n\Translator\ITranslator;

class SidekickTranslator implements ITranslator
{
  use ConfigTrait;

  public function translate($text, $sourceLanguage, $targetLanguage)
  {
    $config       = Container::config()->get("sidekix", new Config());
    $translateApi = $config->getStr("translate_endpoint", null);
    $projectId    = $config->getStr("project_id", 0);
    if($translateApi !== null)
    {
      $postData = [
        'text'      => $text,
        'source'    => $sourceLanguage,
        'target'    => $targetLanguage,
        'projectId' => $projectId,
      ];

      $body   = \Requests::post($translateApi, [], $postData)->body;
      $result = json_decode($body);
      $result = idp($result, 'result', null);
      if($result === null)
      {
        return $text;
      }
      return idp($result, "text", $text);
    }
    else
    {
      throw new \Exception(
        "No Translate Endpoint specified in config file", 400
      );
    }
  }
}
