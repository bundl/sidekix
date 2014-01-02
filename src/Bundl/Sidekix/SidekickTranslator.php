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

  protected $_api;
  protected $_projectId;

  public function __construct()
  {
    $config           = Container::config()->get("sidekix", new Config());
    $this->_api       = $config->getStr("translate_endpoint", null);
    $this->_projectId = $config->getStr("project_id", 0);
  }

  public function translate($text, $sourceLanguage, $targetLanguage)
  {
    if($this->_api !== null)
    {
      $postData = [
        'text'      => $text,
        'source'    => $sourceLanguage,
        'target'    => $targetLanguage,
        'projectId' => $this->_projectId,
      ];

      $body   = \Requests::post($this->_api, [], $postData)->body;
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
