<?php

namespace vale\hcf\sage\models\entitys;

use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\block\Slab;
use pocketmine\block\Stair;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use vale\hcf\sage\models\util\IEManager;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TF;
use vale\hcf\sage\Sage;
use pocketmine\Player;
use vale\hcf\sage\SagePlayer;
use pocketmine\{Server};

class PortableBambeEntity extends Human
{

	const FIND_DISTANCE = 15;

	const LOSE_DISTANCE = 25;

	const ATTACK_DISTANCE = 4;

	/** @var int */
	public $attackDamage;

	/** @var float */
	public $speed;

	/** @var int */
	public $attackWait;

	/** @var int */
	public $regenerationWait = 0;

	/** @var int */
	public $regenerationRate;

	/** @var int[] */
	protected $damages = [];

	/** @var SagePlayer|null */
	private $target = null;

	/** @var int */
	private $findNewTargetTicks = 0;

	/** @var int */
	private $jumpTicks = 5;

	/**
	 * BaseEntity constructor.
	 *
	 * @param Level $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt)
	{
		$manager = new IEManager(Sage::getInstance(), "bambeskin.png");
		$this->setSkin($manager->skin);
		parent::__construct($level, $nbt);
		$this->setMaxHealth(100);
		$this->setNameTagAlwaysVisible(true);
		$this->setCanSaveWithChunk(true);
		$this->width = 1;
		$this->height = 1.8;
		$this->setHealth(150);
		$hp = $this->getHealth();
		$this->setNameTag("§6§lPortable Bambe");
		$this->regenerationRate = 1;
		$this->setAttributes();
	}

	/**
	 * @return string
	 */
	public function getSaveId(): string
	{
		return self::class;
	}
	


	/**
	 * @param EntityDamageEvent $source
	 */
	public function attack(EntityDamageEvent $source): void{
	    	$this->setHealth($this->getHealth() - 10);
	    	
	}
	
	/**
	 * @return bool
	 */
	public function hasTarget(): bool
	{
		$target = $this->getTarget();
		if ($target == null) {
			return false;
		}
		return true;
	}

	/**
	 * @return SagePlayer|null
	 */
	public function getTarget(): ?Player
	{
		return $this->target;
	}
	

	

public function setAttributes() { 
    $sword = Item::get(Item::IRON_INGOT, 0, 1);
    $this->getInventory()->setItemInHand($sword); 
    $this->getArmorInventory()->setHelmet(Item::get(Item::GOLD_HELMET, 0, 1)); 
    $this->getArmorInventory()->setChestplate(Item::get(Item::GOLD_CHESTPLATE, 0, 1));
    $this->getArmorInventory()->setLeggings(Item::get(Item::GOLD_LEGGINGS, 0, 1));
$this->getArmorInventory()->setHelmet(Item::get(Item::GOLD_BOOTS, 0, 1)); 
    
} 
}