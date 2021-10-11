<?php

declare(strict_types = 1);

namespace vale\hcf\sage\tasks\player;

//Base Libraries
use pocketmine\scheduler\Task;
use pocketmine\{Server, Player};
use pocketmine\math\Vector3;
use pocketmine\entity\{Entity, Effect, EffectInstance};
use pocketmine\block\Block;
use pocketmine\item\Item;
//inv
use pocketmine\inventory\Inventory;
//Level
use pocketmine\level\{Location, Level, Position};
use pocketmine\level\sound\GhastSound;
use pocketmine\level\particle\{DestroyBlockParticle, FlameParticle, HugeExplodeSeedParticle};
//Tile
use pocketmine\tile\Tile;
//Core
use vale\hcf\sage\Sage;
//Nbts
use pocketmine\nbt\tag\{CompoundTag, StringTag, IntTag, ListTag};
//Packets
use pocketmine\network\mcpe\protocol\{AddActorPacket, PlaySoundPacket, LevelSoundEventPacket};
use vale\hcf\sage\system\cyberattack\CyberAttack;

class CyberAttackQueueTask extends Task{

	/** @var int|$duration **/
	public static $duration = 100; //3mins

	public function __construct(){
		self::$duration = self::$duration;
	}

	public function onRun($tick){
		--self::$duration;
		if(self::$duration === 100){

		}if(self::$duration === 60){
			Server::getInstance()->broadcastMessage("§r§7The §6§lCyber Attack §r§7event will begin shortly check §6§l/events to see when.");
		}
		if(self::$duration === 40){
			Server::getInstance()->broadcastMessage("§r§7The §6§lCyber Attack §r§7event will begin shortly check §6§l/events to see when.");
		}if(self::$duration === 30){
			Server::getInstance()->broadcastMessage("§r§7The §6§lCyber Attack §r§7event is starting ...");
		}if(self::$duration <= 1){

			if(!Server::getInstance()->isLevelLoaded("world")){
				Server::getInstance()->loadLevel("world");
			}
			$level = Server::getInstance()->getLevelByName("world");
			CyberAttack::spawnEnvoyFallers();
			Server::getInstance()->broadcastMessage("§r§e§l<§6X§e> §r§l§6 CYBER ATTACK EVENT §r§e§l<§6X§e>");
			Server::getInstance()->broadcastMessage("§r§7The cyber attack §6§levent §r§7has started do /coords to find it");
			Server::getInstance()->broadcastMessage("§r§7Head over to the coords to defend our planet from §6§lforeign enemies §r§7claim the crystals to win prizes");
			Server::getInstance()->broadcastMessage("§r§7Tap the §6§lcrystals §r§7to destory them and claim prizes!");
			Server::getInstance()->broadcastMessage("§r§7((CYBER ATTACK EVENT))");
			foreach(Server::getInstance()->getOnlinePlayers() as $player){
				$player->sendPopup("§r§l§6CYBER ATTACK HAS STARTED \n\n\n\n\n\n\n");
			}

			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
			self::$duration = 100;
		}
	}
}
