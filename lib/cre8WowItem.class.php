<?php

class cre8WowItem implements cre8WowArmoryInterface
{

  private $url;
  private $id;

  /**
   *
   * @var SimpleXMLElement
   */
  protected $xmlsheet = null;

  public function  __construct($id) {
    $this->id = $id;
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
    $this->url = 'item-info.xml';
  }

  public function getUrlParameters() {
    return array('i' => $this->getId());
  }

  public function getId() {
    return $this->id;
  }

  public function isValid() {
    return $this->xmlsheet->itemInfo->item['name'] ? true : false;
  }

  public function getName() {
    return $this->xmlsheet->itemInfo->item['name'];
  }

  public function getLevel() {
    return $this->xmlsheet->itemInfo->item['level'];
  }

  public function getQuality() {
    return $this->xmlsheet->itemInfo->item['quality'];
  }

  public function getType() {
    return $this->xmlsheet->itemInfo->item['type'];
  }

  public function getIcon() {
    return $this->xmlsheet->itemInfo->item['icon'];
  }


}