<?php

declare(strict_types = 1);

namespace vale\hcf\sage\models\entitys;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\MenuIds;
use pocketmine\block\BoneBlock;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\Server;
use vale\hcf\sage\kits\KitManager;
use vale\hcf\sage\models\util\CrystalInventory;
use vale\hcf\sage\models\util\IEManager;
use vale\hcf\sage\models\util\PartnerCrateInventory;
use vale\hcf\sage\models\util\UtilManager;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\system\deathban\Deathban;

class PartnerPackageEntity extends Human {


	const NETWORK_ID =  1;
	/**
	 * BaseEntity constructor.
	 *
	 * @param Level $level
	 * @param CompoundTag $nbt
	 * @param Player $player
	 *
	 */
	public function __construct(Level $level, CompoundTag $nbt) {
		$manager = new IEManager(Sage::getInstance(), "partnerpackage.png");
		$this->setSkin($manager->skin);
		parent::__construct($level, $nbt);
		$this->setMaxHealth(4);
		$this->setNameTag(self::getNPCName());
		$this->setNameTagAlwaysVisible(true);
		$this->width = 1;
		$this->height = 1.8;
		$this->setHealth(4);
		$this->setScale(1);
		$this->yaw = $this->getYaw();
		$this->getInventory()->setItemInHand(Item::get(Item::ENDER_CHEST, 0, 1));
		$this->setCanSaveWithChunk(true);
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return "aDPDPD";
	}

	/**
	 * @param EntityDamageEvent $event
	 * @priority NORMAL
	 */

	public function attack(EntityDamageEvent $event) : void
	{
		if ($event instanceof EntityDamageByEntityEvent) {
			$damager = $event->getDamager();
			if ($damager instanceof SagePlayer) {
				$entity = $event->getEntity();
				$menu =  new PartnerCrateInventory();
				$menu->sendPartnerCrateInventory($damager);
				$event->setCancelled(true);
			}
		}
	}


	public static function getNPCName(): string{
		$line = [
			"??r??a??l* NEW *",
			str_repeat(" ", 3),
			"\n??r??6Partner Crates\n",
			"??r??fLeft Click for Rewards!",
			"\n??r??fRight Click to Open",
			str_repeat(" ", 2),
			"\n??r??fstore.ourhcfserver.net!!\n\n",
		];
		#foreach($val as $line){

		return ($line[0] . "\n" . $line[1] . $line[2] . $line[3] . $line[4] . "\n" . $line[5] . $line[6] . "\n\n\n");
		# }
	}

	public function entityBaseTick(int $tickDiff = 1): bool
	{
		return parent::entityBaseTick($tickDiff); // TODO: Change the autogenerated stub
	}
}



