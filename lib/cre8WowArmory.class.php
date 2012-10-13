<?php

class cre8WowArmory implements cre8WowArmoryInterface {

  protected $url;
  protected $hasError = false;
  protected $errorMsg = '';

  /**
   *
   * @var sfWebBrowser
   */
  protected $browser = null;
  protected $region = null;
  protected $server = null;

  /**
   *
   * @var cre8WowItem
   */
  protected $item = null;

  /**
   *
   * @param string $server Server name (example: [Kul Tiras])
   * @param string $region Region. Supported regions: [eu, us, kr, cn, tw]
   */
  public function __construct($server, $region = 'eu', $headers = array()) {
    $this->region = strtolower($region);
    $this->server = ucwords(strtolower($server));
    $this->setUrl();
    $headers = array_merge($headers, array(
            'User-Agent' => "Mozilla/5.0 (X11; U; Linux x86_64; pl-PL; rv:1.9.1.8) Gecko/20100214 Ubuntu/9.10 (karmic) Firefox/3.5.8"
    ));
    $this->browser = new sfWebBrowser($headers, 'sfCurlAdapter');
  }

  public function getRegion() {
    return $this->region;
  }

  public function getServer() {
    return $this->server;
  }

  public function getUrl($url = '') {
    return $this->url . $url;
  }

  public function setUrl() {
    $url = 'http://';
    if($this->getRegion() != 'us') {
      $url .= $this->getRegion();
    } else {
      $url .= 'www';
    }
    $url .= '.wowarmory.com/';
    $this->url = $url;
  }

  public function getUrlParameters($parameters = array()) {
    return array_merge(array(
            'r' => (string) $this->getServer()
            ), $parameters);
  }

  protected function getContent($url, $parameters = array()) {
    try {
      if (!$this->browser->get($url, $parameters)->responseIsError()) {
        $this->hasError = false;
        $this->errorMsg = '';
        return $this->browser->getResponseText(); // Successful response (eg. 200, 201, etc)
      }
      else {
        new Exception('error response');  // Error response (eg. 404, 500, etc)
      }
    }
    catch (Exception $e) {
      $this->errorMsg = $e->getMessage(); // Adapter error (eg. Host not found)
      $this->hasError = true;
    }
    return null;
  }

  /**
   *
   * @param string $name Character name
   * @return cre8WowCharacter Returns cre8WowCharacter object or null
   */
  public function getCharacter($name) {
    $character = new cre8WowCharacter($name);
    $content = $this->getContent($this->getUrl($character->getUrl()), $this->getUrlParameters($character->getUrlParameters()));
    if(!$this->hasError && $content) {
      $character->setXML($content);
      return $character->isValid() ? $character : null;
    }
    return null;
  }

  /**
   *
   * @param string $name Guild name
   * @return cre8WowGuild Returns cre8WowGuild Object or null
   */
  public function getGuild($name) {
    $guild = new cre8WowGuild($name);
    $content = $this->getContent($this->getUrl($guild->getUrl()), $this->getUrlParameters($guild->getUrlParameters()));
    if(!$this->hasError && $content) {
      $guild->setXML($content);
      return $guild->isValid() ? $guild : null;
    }
    return null;
  }

  /**
   *
   * @param integer $ID Item's ID number
   * @return cre8WowItem Return cre8WowItem object or null.
   */
  public function getItem($ID) {
    if($this->item) {
      return $this->item;
    }
    $item = new cre8WowItem($ID);
    $content = $this->getContent($this->getUrl($item->getUrl()), $this->getUrlParameters($item->getUrlParameters()));
    if(!$this->hasError && $content) {
      $item->setXML($content);
      if($item->isValid()) {
        $this->item = $item;
      }
    }
    return $this->item;
  }

}