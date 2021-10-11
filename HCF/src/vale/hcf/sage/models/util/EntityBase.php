<?php

declare(strict_types = 1);

namespace vale\hcf\sage\models\util;

use pocketmine\item\{Item, enchantment\Enchantment, enchantment\EnchantmentInstance};
use pocketmine\entity\{Entity, Monster, Living};
use pocketmine\inventory\ArmorInventory;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;

class EntityBase extends Monster{
	const NETWORK_ID = self::WITHER_SKELETON;

	public $width = 0.7;
	public $height = 2.4;

	public function getName(): string{
		return "an Envoy Guardian";
	}

	public function initEntity(): void{
		$this->setMaxHealth(40);
		#$this->setEquipment();
		parent::initEntity();
	}

	public function getDrops(): array{
		return [
			#Item::get(Item::COAL, 0, mt_rand(0, 1)),
			#Item::get(Item::BONE, 0, mt_rand(0, 2)),
		];
	}
}
