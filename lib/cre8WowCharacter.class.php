<?php

/**
 * Description of cre8WowCharacterclass
 *
 * @author tehcrow
 */
class cre8WowCharacter implements cre8WowArmoryInterface {

  private $url;
  private $name;

  private $items = array();

  /**
   *
   * @var SimpleXMLElement
   */
  protected $xmlsheet = null;

  public function  __construct($name) {
    $this->name = $name;
    $this->setUrl();

    if(!defined('SECONDBAR_TYPE_MANA')) define('SECONDBAR_TYPE_MANA',"m");
    if(!defined('SECONDBAR_TYPE_RAGE')) define('SECONDBAR_TYPE_RAGE',"r");
    if(!defined('SECONDBAR_TYPE_RUNIC')) define('SECONDBAR_TYPE_RUNIC',"p");

    if(!defined('BASESTAT_STRENGHT')) define('BASESTAT_STRENGHT',0);
    if(!defined('BASESTAT_AGILITY')) define('BASESTAT_AGILITY',1);
    if(!defined('BASESTAT_STAMINA')) define('BASESTAT_STAMINA',2);
    if(!defined('BASESTAT_INTELLECT')) define('BASESTAT_INTELLECT',3);
    if(!defined('BASESTAT_SPIRIT')) define('BASESTAT_SPIRIT',4);
    if(!defined('BASESTAT_ARMOR')) define('BASESTAT_ARMOR',5);

    if(!defined('RESISTANCE_ARCANE')) define('RESISTANCE_ARCANE',0);
    if(!defined('RESISTANCE_FIRE')) define('RESISTANCE_FIRE',1);
    if(!defined('RESISTANCE_FROST')) define('RESISTANCE_FROST',2);
    if(!defined('RESISTANCE_HOLY')) define('RESISTANCE_HOLY',3);
    if(!defined('RESISTANCE_NATURE')) define('RESISTANCE_NATURE',4);
    if(!defined('RESISTANCE_SHADOW')) define('RESISTANCE_SHADOW',5);

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
    $this->url = 'character-sheet.xml';
  }

  public function getUrlParameters() {
    return array('cn' => $this->getName());
  }

  public function getName() {
    return $this->name;
  }

  public function isValid() {
    return $this->xmlsheet->characterInfo->character["classId"] ? true : false;
  }

  /**
   *
   * @return string Name prefix
   */
	function getNamePrefix() {
		return trim($this->xmlsheet->characterInfo->character["prefix"]);
	}

	/**
   *
   * @return string Name suffix
   */
	function getNameSuffix() {
		return trim($this->xmlsheet->characterInfo->character["suffix"]);
	}

	/**
   *
   * @return string Class
   */
	function getClass() {
		return $this->xmlsheet->characterInfo->character["class"];
	}

	/**
   *
   * @return integer Class ID
   */
	function getClassId() {
		return $this->xmlsheet->characterInfo->character["classId"];
	}

	/**
   *
   * @return string Faction
   */
	function getFaction() {
		return $this->xmlsheet->characterInfo->character["faction"];
	}

	/**
   *
   * @return integer Faction's ID
   */
	function getFactionId() {
		return $this->xmlsheet->characterInfo->character["factionId"];
	}

	/**
   *
   * @return string Gender
   */
	function getGender() {
		return $this->xmlsheet->characterInfo->character["gender"];
	}

	/**
   *
   * @return integer Gender's ID
   */
	function getGenderId() {
		return $this->xmlsheet->characterInfo->character["genderId"];
	}

	/**
   *
   * @return string Guild name
   */
	function getGuildName() {
		return $this->xmlsheet->characterInfo->character["guildName"];
	}

  /**
   *
   * @return string Armory's guild's URL
   */
	function getGuildUrl() {
		return $this->char_url.$this->xmlsheet->characterInfo->character["guildUrl"];
	}

	/**
   *
   * @return string Last modified time stamp [dd Month yyyy]
   */
	function getLastModified() {
		return $this->xmlsheet->characterInfo->character["lastModified"];
	}

	/**
   *
   * @return integer Level
   */
	function getLevel() {
		return $this->xmlsheet->characterInfo->character["level"];
	}

	/**
   *
   * @return string Achievement total points.
   */
	function getAchievementPoints() {
		return (int) $this->xmlsheet->characterInfo->character["points"];
	}

	/**
   *
   * @return string Race
   */
	function getRace() {
		return $this->xmlsheet->characterInfo->character["race"];
	}

	/**
   *
   * @return string Race's ID
   */
	function getRaceId() {
		return $this->xmlsheet->characterInfo->character["raceId"];
	}

	/**
   *
   * @return string Realm name
   */
  function getRealm() {
		return $this->xmlsheet->characterInfo->character["realm"];
	}

	/**
   *
   * @return array Primary spec, returns if it's the active spec, the main spec and the number of talent points used in each tree. isActive, (String) mainTree, (Integer) treeOne, (Integer) treeTwo, (Integer) treeThree.
   */
	function getPrimarySpec() {
		foreach($this->xmlsheet->characterInfo->characterTab->talentSpecs->talentSpec as $specs => $data) {
			if($data['group'] == 1) {
				$return = array (
						"isActive" => (boolean) $data['active'],
						"mainTree" => (string) $data['prim'],
						"treeOne" => (int) $data['treeOne'],
						"treeTwo" => (int) $data['treeTwo'],
						"treeThree" => (int) $data['treeThree']
					);
			}
		}
		return $return['mainTree'] ? $return : $this->getSecondarySpec();
	}

	/**
	  (Array) Secondary spec, returns NULL if there is no secondary spec.
	  @see wowasdk_character::getPrimarySpec
	*/
	function getSecondarySpec() {
		foreach($this->xmlsheet->characterInfo->characterTab->talentSpecs->talentSpec as $specs => $data) {
			if($data['group'] == 1) continue;
			$return = array (
					"isActive" => (boolean) $data['active'],
					"mainTree" => (string) $data['prim'],
					"treeOne" => (int) $data['treeOne'],
					"treeTwo" => (int) $data['treeTwo'],
					"treeThree" => (int) $data['treeThree']
				);
		}
		if(isset($return)) {
			if($return['isActive'] == "") $return['isActive'] = false;
			else $return['isActive'] = true;
		} else {
      $return = array();
    }
		return $return;
	}

	/**
	  (Array) Active spec, returns the main spec and the number of talent points used in each tree.
	  (String) mainTree, (Integer) treeOne, (Integer) treeTwo, (Integer) treeThree
	*/
	function getActiveSpec() {
		foreach($this->xmlsheet->characterInfo->characterTab->talentSpecs->talentSpec as $specs => $data) {
			if($data['active'] != 1) continue;
			$return = array (
					"mainTree" => $data['prim'],
					"treeOne" => $data['treeOne'],
					"treeTwo" => $data['treeTwo'],
					"treeThree" => $data['treeThree']
				);
		}
		return $return;
	}

	/**
	  (Integer) Life-time honorable kills.
	*/
	function getLifetimeHonorableKills() {
		return $this->xmlsheet->characterInfo->characterTab->pvp->lifetimehonorablekills['value'];
	}

	/**
	  (Array) Primary profesions: return[0/1] => ["name" (String)] = profession name, ["skill" (Integer)] skill value.
	*/
	function getProfessions() {
		$return = array();
    foreach($this->xmlsheet->characterInfo->characterTab->professions->skill as $skill => $data) {
			$return[] = array(
				"name" => (string) $data["name"],
				"value" => (int) $data["value"],
        "id" => (int) $data['id'],
        "key" => (string) $data['key'],
        'max' => (int) $data['max']
			);
		}
    return $return;
	}

	/**
	  (Integer) Maximum health.
	*/
	function getMaxHealth() {
		return $this->xmlsheet->characterInfo->characterTab->characterBars->health["effective"];
	}

	/**
	  (Array) The second bar (mana, rage, runic): return["type"] = SECONDBAR_TYPE_*, return["maxValue"] = maximum value of the second bar.
	*/
	function getSecondBar() {
		return array(
			"type" => $this->xmlsheet->characterInfo->characterTab->characterBars->secondBar["type"],
			"maxValue" => $this->xmlsheet->characterInfo->characterTab->characterBars->secondBar["effective"]
		);
	}

	/**
	  (Integer) Effective base statistic value, returns null if $stat is not valid.
	  @param $stat BASESTAT_*
	*/
	function getBaseStat($stat) {
		switch($stat) {
			case BASESTAT_STRENGHT:
				return $this->xmlsheet->characterInfo->characterTab->baseStats->strenght["effective"];
			case BASESTAT_AGILITY:
				return $this->xmlsheet->characterInfo->characterTab->baseStats->agility["effective"];
			case BASESTAT_STAMINA:
				return $this->xmlsheet->characterInfo->characterTab->baseStats->stamina["effective"];
			case BASESTAT_INTELLECT:
				return $this->xmlsheet->characterInfo->characterTab->baseStats->intellect["effective"];
			case BASESTAT_SPIRIT:
				return $this->xmlsheet->characterInfo->characterTab->baseStats->spirit["effective"];
			case BASESTAT_ARMOR:
				return $this->xmlsheet->characterInfo->characterTab->baseStats->armor["effective"];
			default:
				return NULL;
		}
		return NULL;
	}

	/**
	  (Integer) Resistance to element, returns null if $element is not valid.
	  @param $element RESISTANCE_*
	*/
	function getResistance($element) {
		switch($element) {
			case RESISTANCE_ARCANE:
				return $this->xmlsheet->characterInfo->characterTab->resistances->arcane["value"];
			case RESISTANCE_FIRE:
				return $this->xmlsheet->characterInfo->characterTab->resistances->fire["value"];
			case RESISTANCE_FROST:
				return $this->xmlsheet->characterInfo->characterTab->resistances->frost["value"];
			case RESISTANCE_HOLY:
				return $this->xmlsheet->characterInfo->characterTab->resistances->holy["value"];
			case RESISTANCE_NATURE:
				return $this->xmlsheet->characterInfo->characterTab->resistances->nature["value"];
			case RESISTANCE_SHADOW:
				return $this->xmlsheet->characterInfo->characterTab->resistances->shadow["value"];
		}
		return NULL;
	}

	/**
	  (Array) Main hand damage stats.
	  Return -> ["dps"], ["max"], ["min"], ["percent"], ["speed"].
	*/
	function getMainHandDamage() {
		return array(
			"dps" => $this->xmlsheet->characterInfo->characterTab->melee->mainHandDamage["dps"],
			"max" => $this->xmlsheet->characterInfo->characterTab->melee->mainHandDamage["max"],
			"min" => $this->xmlsheet->characterInfo->characterTab->melee->mainHandDamage["min"],
			"percent" => $this->xmlsheet->characterInfo->characterTab->melee->mainHandDamage["percent"],
			"speed" => $this->xmlsheet->characterInfo->characterTab->melee->mainHandDamage["speed"]
		);
	}

	/**
	  (Array) Main hand speed stats.
	  Return -> ["hastePercent"], ["hasteRating"], ["speed"] (seconds/hit).
	*/
	function getMainHandSpeed() {
		return array(
			"hastePercent" => $this->xmlsheet->characterInfo->characterTab->melee->mainHandSpeed["hastePercent"],
			"hasteRating" => $this->xmlsheet->characterInfo->characterTab->melee->mainHandSpeed["hasteRating"],
			"speed" => $this->xmlsheet->characterInfo->characterTab->melee->mainHandSpeed["speed"]

		);
	}

	/**
	  (Array) Off hand damage stats.
	  @see wowasdk_character::getMainHandDamage
	*/
	function getOffHandDamage() {
		return array(
			"dps" => $this->xmlsheet->characterInfo->characterTab->melee->offHandDamage["dps"],
			"max" => $this->xmlsheet->characterInfo->characterTab->melee->offHandDamage["max"],
			"min" => $this->xmlsheet->characterInfo->characterTab->melee->offHandDamage["min"],
			"percent" => $this->xmlsheet->characterInfo->characterTab->melee->offHandDamage["percent"],
			"speed" => $this->xmlsheet->characterInfo->characterTab->melee->offHandDamage["speed"]
		);
	}

	/**
	  (Array) Off hand speed stats.
	  @see wowasdk_character::getMainHandSpeed
	*/
	function getOffHandSpeed() {
		return array(
			"hastePercent" => $this->xmlsheet->characterInfo->characterTab->melee->offHandSpeed["hastePercent"],
			"hasteRating" => $this->xmlsheet->characterInfo->characterTab->melee->offHandSpeed["hasteRating"],
			"speed" => $this->xmlsheet->characterInfo->characterTab->melee->offHandSpeed["speed"]

		);
	}

	/**
	  (Array) Melee power stats.
	  Return -> ["base"] (base value), ["effective"] (objects-empowered value), ["increasedDps"] (increased DPS).
	*/
	function getMeleePower() {
		return array(
			"base" => $this->xmlsheet->characterInfo->characterTab->melee->power["base"],
			"effective" => $this->xmlsheet->characterInfo->characterTab->melee->power["effective"],
			"increasedDps" => $this->xmlsheet->characterInfo->characterTab->melee->power["increasedDps"]
		);
	}

	/**
	  (Array) Melee hit rating stats.
	  Return -> ["percent"], ["penetration"] (increased armor penetration), ["armorPercent"] (armor penetration bonus percent), ["value"].
	*/
	function getMeleeHitRating() {
		return array(
			"percent" => $this->xmlsheet->characterInfo->characterTab->melee->hitRating["increasedHitPercent"],
			"penetration" => $this->xmlsheet->characterInfo->characterTab->melee->hitRating["penetration"],
			"armorPercent" => $this->xmlsheet->characterInfo->characterTab->melee->hitRating["reducedArmorPercent"],
			"value" => $this->xmlsheet->characterInfo->characterTab->melee->hitRating["value"]
		);
	}

	/**
		(Array) Melee critic hit chance stats.
		Return -> ["percent"] (base stat percent), ["plusPercent"] (objects-based percent bonus), ["rating"] (raw number).
	*/
	function getMeleeCritChance() {
		return array(
			"percent" => $this->xmlsheet->characterInfo->characterTab->melee->critChance["percent"],
			"plusPercent" => $this->xmlsheet->characterInfo->characterTab->melee->critChance["plusPercent"],
			"rating" => $this->xmlsheet->characterInfo->characterTab->melee->critChance["rating"]
		);
	}

	/**
		(Array) Expertise stats.
		Return -> ["additional"], ["percent"], ["rating"], ["value"].
	*/
	function getExpertise() {
		return array(
			"additional" => $this->xmlsheet->characterInfo->characterTab->melee->expertise["additional"],
			"percent" => $this->xmlsheet->characterInfo->characterTab->melee->expertise["percent"],
			"rating" => $this->xmlsheet->characterInfo->characterTab->melee->expertise["rating"],
			"value" => $this->xmlsheet->characterInfo->characterTab->melee->expertise["value"]
		);
	}

  public function getItems() {
    if($this->items) {
      return $this->items;
    }
    foreach($this->xmlsheet->characterInfo->characterTab->items->item as $item => $data) {
      $slot = (int) $data['slot'];
      if(!($slot >= 0 && $slot <= 18)) {
        continue;
      }
      $this->items[$slot] = array(
        'id' => (int) $data['id'],
        'name' => (string) $data['name'],
        'level' => (int) $data['level'],
        'rarity' => (int) $data['rarity'],
        'icon' => (string) $data['icon'],
        'displayInfoId' => (int) $data['displayInfoId']

      );
      $i=0;
      $gems = array();
      while(isset($data['gem'.$i.'Id']) && ($data['gem'.$i.'Id'] != 0)) {
        $gems[$i] = array(
          'id' => (int) $data['gem'.$i.'Id'],
          'icon' => (string) $data['gemIcon'.$i]
        );
        $i++;
      }
      $this->items[$slot]['gems'] = $gems;
      $enchants = array();
      if($data['permanentenchant'] != 0 && ((int) $data['permanentEnchantItemId']) != 0) {
        $enchants[] = array(
          'id' => (int) $data['permanentEnchantItemId'],
          'icon' => (string) $data['permanentEnchantIcon']
        );
      }
      $this->items[$slot]['enchants'] = $enchants;
    }
    return $this->items;
  }

}
?>
