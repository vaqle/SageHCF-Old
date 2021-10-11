<?php

namespace vale\hcf\libaries;

use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class ScoreboardFactory {

	/** @var array $scoreboards[] */
	protected static $scoreboards = [];

	/**
	 * @param SagePlayer $player
	 * @param String $objectiveName
	 * @param String $displayName
	 * @return void
	 */
	public function newScoreboard(SagePlayer $player, String $objectiveName, String $displayName) : void {
		if(isset(self::$scoreboards[$player->getName()])){
			unset(self::$scoreboards[$player->getName()]);
		}
		$pk = new SetDisplayObjectivePacket();
		$pk->displaySlot = "sidebar";
		$pk->objectiveName = $objectiveName;
		$pk->displayName = $displayName;
		$pk->criteriaName = "dummy";
		$pk->sortOrder = 0;
		$player->sendDataPacket($pk);
		self::$scoreboards[$player->getName()] = $objectiveName;
	}

	/**
	 * @param SagePlayer $player
	 * @return void
	 */
	public function removePrimary(SagePlayer $player) : void {
		if(isset(self::$scoreboards[$player->getName()])){
			$objectiveName = $this->getObjectiveName($player);
			$pk = new RemoveObjectivePacket();
			$pk->objectiveName = $objectiveName;
			$player->sendDataPacket($pk);
			unset(self::$scoreboards[$player->getName()]);
		}
	}

	/**
	 * @param SagePlayer $player
	 * @return void
	 */
	public function remove(SagePlayer $player, $key) : void {
		if(isset(self::$scoreboards[$player->getName()])){
			$objectiveName = $this->getObjectiveName($player);
			$pk = new RemoveObjectivePacket();
			$pk->objectiveName = $objectiveName;
			$player->sendDataPacket($pk);
			unset(self::$scoreboards[$player->getName()], $key);
		}
	}

	/**
	 * @param SagePlayer $player
	 * @param Int $score
	 * @param String $message
	 * @return void
	 */
	public function setLine(SagePlayer $player, Int $score, ?String $message) : void {
		if(!isset(self::$scoreboards[$player->getName()])){
			Sage::getInstance()->getLogger()->info("Error");
			return;
		}
		if($score > 15){
			Sage::getInstance()->getLogger()->info("Error, you exceeded the limit of parameters 1-15");
			return;
		}
		$objectiveName = $this->getObjectiveName($player);
		$entry = new ScorePacketEntry();
		$entry->objectiveName = $objectiveName;
		$entry->type = $entry::TYPE_FAKE_PLAYER;
		$entry->customName = $message;
		$entry->score = $score;
		$entry->scoreboardId = $score;
		$pk = new SetScorePacket();
		$pk->type = $pk::TYPE_CHANGE;
		$pk->entries[] = $entry;
		$player->sendDataPacket($pk);
	}

	/**
	 * @param SagePlayer $player
	 * @return String
	 */
	public function getObjectiveName(SagePlayer $player) : ?String {
		return isset(self::$scoreboards[$player->getName()]) ? self::$scoreboards[$player->getName()] : null;
	}
}