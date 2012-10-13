<?php

/**
 * Description of cre8WowCharacterclass
 *
 * @author tehcrow
 */
class cre8WowGuild implements cre8WowArmoryInterface {

  private $url;
  private $name;
  private $members = array();

  /**
   *
   * @var SimpleXMLElement
   */
  protected $xmlsheet = null;

  public function  __construct($name) {
    $this->name = $name;
    $this->setUrl();

  }

  public function setXML($xml) {
    if($xml) {
      $this->xmlsheet = simplexml_load_string($xml);
    }
  }

  public function getXML() {
    return (! is_null($this->xmlsheet) && ($this->xmlsheet instanceof SimpleXMLElement)) ? $this->xmlsheet->asXML() : null;
  }

  public function getUrl() {
    return $this->url;
  }

  public function setUrl() {
    $this->url = 'guild-info.xml';
  }

  public function getUrlParameters() {
    return array('gn' => (string) $this->getName());
  }

  public function getName() {
    return $this->name;
  }

  public function isValid() {
    return $this->xmlsheet->guildInfo->guildHeader['name'] ? true : false;
  }
  
  public function getMembers() {
    if($this->members) {
      return $this->members;
    }
    foreach($this->xmlsheet->guildInfo->guild->members->character as $key => $val) {
      $this->members[] = $val;
    }
    return $this->members;
  }

  public function getMember($characterName) {
    foreach($this->getMembers() as $member) {
      if($member['name'] == $characterName) {
        return $member;
      }
    }
    return null;
  }

  public function getCharacterRank($characterName) {
    if($member = $this->getMember($characterName)) {
      return (int) $member['rank'];
    }
    return null;
  }

  public function getFactionId() {
    return $this->xmlsheet->guildInfo->guildHeader['faction'];
  }

}