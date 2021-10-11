<?php

declare(strict_types = 1);

#   /\ /\___ _ __ __ _ ___
#   \ \ / / _ \ '__/ _`|/_\
#    \ V / __/ | | (_| |__/
#     \_/ \___ |_| \__,|\___
#                  |___/

namespace vale\hcf\sage\system\cyberattack;

use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\{Server, Player};
use pocketmine\nbt\tag\{CompoundTag, ListTag};
use pocketmine\level\Position;
use vale\hcf\sage\system\cyberattack\CyberEntity;
use pocketmine\event\entity\{EntityDamageEvent, EntityDamageByEntityEvent};
use pocketmine\level\Level;
use pocketmine\{block\Block, level\particle\DestroyBlockParticle, network\mcpe\protocol\types\ContainerIds};

class CyberAttack extends Entity{
	public const NETWORK_ID = self::SKELETON;


//Warzone Map
	private const WARZONE = "world";
	public $width = 0.6;
	public $height = 1.99;
	protected $gravity = 0.03;
	public static $dt = 200*20;

	private static $positions = [
[91, 68, -601],
[90, 74, -648],
[102, 70, -698],
[150, 68, -711],
[206, 72, -703],
[221, 70, -638],
[211, 74, -576],
[221, 70, -638],
[163, 72, -571],
[132, 73, -575],
];

	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);

	}

	protected function initEntity() : void{
		parent::initEntity();

		$this->setMaxHealth(5);
		$this->setHealth(5);
		$this->setScale(6);
		$this->setCanSaveWithChunk(false);
		$this->setInvisible(true);
		$item = new Item(397, 5);
		$item->setNamedTagEntry(new ListTag("ench"));

	}

	public function entityBaseTick(int $tickDiff = 1) : bool{

		$hasUpdate = parent::entityBaseTick($tickDiff);


		if($this->isOnGround()){
			$this->flagForDespawn();
			$this->level->loadChunk((int)$this->x, (int)$this->z, true);
			CyberEntity::spawnEnvoy(new Position($this->x, $this->y +3, $this->z, $this->getLevel()));
		}
		if($this->yaw > 360){
			$this->yaw = 0;
		}else{
			$this->yaw += 15;
		}


		return $hasUpdate;
	}


	public function canCollideWith(Entity $entity) : bool{
		return false;
	}

	public function canBeCollidedWith() : bool{
		return false;
	}
	/**
	 * @param EntityDamageEvent $event
	 */

	public function attack(EntityDamageEvent $event) : void{
		if($event instanceof EntityDamageByEntityEvent){
			$entity = $event->getEntity();
			$damager = $event->getDamager();
			if($damager instanceof Player){
				$event->setCancelled();
				#$damager->sendMessage("crate test");
				$entity->getLevel()->addParticle(new DestroyBlockParticle($entity->asVector3(), Block::get(Block::GLOWING_OBSIDIAN)));
			}
		}
	}



	public static function spawnFaller(Position $pos) :self{
		$entity = new self($pos->getLevel(), self::createBaseNBT($pos));
		$entity->spawnToAll();
		return $entity;
	}

	public static function spawnEnvoyFallers() : void{
		if(!Server::getInstance()->isLevelLoaded(self::WARZONE)){
			Server::getInstance()->loadLevel(self::WARZONE);
		}
		foreach(self::$positions as $pos){
			Server::getInstance()->getLevelByName(self::WARZONE)->loadChunk($pos[0], $pos[2], true);
			self::spawnFaller(new Position($pos[0], $pos[1] + 80, $pos[2], Server::getInstance()->getLevelByName(self::WARZONE)));

		}
	}

	public static function loadMeteorChunks() : void{
		foreach(self::$positions as $pos){
			Server::getInstance()->getLevelByName(self::WARZONE)->loadChunk($pos[0], $pos[2], true);
		}
	}
}