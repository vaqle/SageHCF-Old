<?php

namespace vale\hcf\sage\models\entitys;

use pocketmine\block\Gold;
use pocketmine\entity\Entity;
use pocketmine\entity\Villager;
use pocketmine\entity\Zombie;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\utils\TextFormat;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class PlayerLogger extends Zombie {

	/** @var string */
	private $name = "";

	/** @var int */
	private $time;

	public function initEntity(): void {
		parent::initEntity();
		$this->setMaxHealth(200);
		$this->setHealth(200);
		$this->getArmorInventory()->setHelmet(Item::get(Item::GOLD_HELMET));
	}

	/**
	 * @param int $tickDiff
	 *
	 * @return bool
	 */
	public function entityBaseTick(int $tickDiff = 1): bool {
		parent::entityBaseTick($tickDiff);
		$server = Sage::getInstance()->getServer();
		if($server->getPlayer($this->name) !== null) {
			$this->flagForDespawn();
			return false;
		}
		if((!$this->isAlive()) and (!$this->closed)) {
			$this->flagForDespawn();
			return false;
		}
		if($this->name === null) {
			return false;
		}
		$time = 160 - (time() - $this->time);
		if($time <= 0) {

			$this->getLevel()->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_HURT);
			$this->flagForDespawn();
			return false;
		}
		$minutes = floor($time / 60);
		$seconds = $time % 60;
		if($seconds < 10) {
			$seconds = "0$seconds";
		}
		$this->setNameTag("§c§l*OFFLINE* " . "§r§f" . $this->name . "§r§e§l " .   floor($this->getHealth())  . " §6§lHP §r§6" .  " $minutes:$seconds");
		return $this->isAlive();
	}

	public function attack(EntityDamageEvent $source): void {
		if(($this->getHealth() - $source->getFinalDamage()) > 0) {
			parent::attack($source);
			return;
		}

		$drops = [];
		$server = Sage::getInstance()->getServer();

		$namedTag = $server->getOfflinePlayerData($this->name);
		$items = $namedTag->getListTag("Inventory")->getAllValues();
		foreach($items as $item) {
			$item = Item::nbtDeserialize($item);
			$drops[] = $item;
		}
		$level = $server->getDefaultLevel();
		$spawn = $level->getSpawnLocation();
		$namedTag->setTag(new ListTag("Inventory", [], NBT::TAG_Compound));
		$namedTag->setTag(new ListTag("Pos", [
			new DoubleTag("", $spawn->x),
			new DoubleTag("", $spawn->y),
			new DoubleTag("", $spawn->z)
		], NBT::TAG_Double));
		$namedTag->setTag(new StringTag("Level", $level->getFolderName()));
		$server->saveOfflinePlayerData($this->name, $namedTag);
		foreach($drops as $item) {
			$this->getLevel()->dropItem($this, $item);
		}

		$this->getLevel()->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_HURT);
		$this->flagForDespawn();
	}

	/**
	 * @param Entity $attacker
	 * @param float $damage
	 * @param float $x
	 * @param float $z
	 * @param float $base
	 */
	public function knockBack(Entity $attacker, float $damage, float $x, float $z, float $base = 0.4): void {

	}


	public function setPlayer(SagePlayer $player): void {
		$this->name = $player->getName();
		$this->setNameTag(TextFormat::YELLOW . TextFormat::BOLD . $player->getName());
		$this->time = time();
	}
}