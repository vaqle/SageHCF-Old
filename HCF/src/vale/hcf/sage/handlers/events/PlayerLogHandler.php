<?php

namespace vale\hcf\sage\handlers\events;

use pocketmine\entity\Zombie;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use vale\hcf\sage\models\entitys\PlayerLogger;
use vale\hcf\sage\models\entitys\PotionSpawnerEntity;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerQuitEvent;

class PlayerLogHandler implements Listener
{
	public $core;

	public function __construct(Sage $core)
	{
		$this->core = $core;
	}

	public function onLoggerQuit(PlayerQuitEvent $event)
	{
		$player = $event->getPlayer();
		$plevel = Sage::getInstance()->getServer()->getLevelByName($player->getLevel()->getName());
		$level = Sage::getInstance()->getServer()->getLevelByName("deathban");
		if ($player instanceof SagePlayer) {
			if ($player instanceof SagePlayer) {
				$arr = ["spawn", "Spawn", "Deathban Arena"];
				if (in_array(Sage::getInstance()::getFactionsManager()->getClaimer($player->getX(), $player->getZ()), $arr)) {
					return;
				}
			}
			if ($player->hasPvpTimer()) {
				return;
			}

			if ($plevel === $level) {
				return;
			}
			   if($player->getGamemode() === SagePlayer::SURVIVAL){
				$bot = new PlayerLogger($player->getLevel(), Entity::createBaseNBT($player->asVector3()));
				$bot->setPlayer($player);
				$bot->width = 1;
				$bot->height = 1.8;
				$player->getLevel()->addEntity($bot);
				$bot->spawnToAll();
			}
		}
	}


	public function onEntityDamage(EntityDamageByEntityEvent $event)
	{
		$entity = $event->getEntity();
		if (Sage::getFactionsManager()->isSpawnClaim($entity)) {
			$event->setCancelled(true);
		}
	}
}