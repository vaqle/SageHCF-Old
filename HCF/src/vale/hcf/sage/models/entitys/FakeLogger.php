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

class FakeLogger extends Zombie {

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