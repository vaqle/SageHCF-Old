<?php

namespace vale\hcf\sage\models\blocks;

use pocketmine\block\Chest as PMChest;
use pocketmine\item\EnderPearl;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\EnderPearl as PMEnderpearl;
use pocketmine\nbt\tag\{FloatTag, CompoundTag, DoubleTag, ListTag, ShortTag};
use vale\hcf\sage\Sage;
use vale\hcf\sage\tasks\player\PearlingTask;

class Chest extends PMChest{
	public function onActivate(Item $item, Player $player = null) : bool{
		if($item instanceof EnderPearl){
			if($player->getGamemode() === 1){
				if ($player->isEnderPearl()) {
					return false;
				}
				$player->setEnderPearl(true);
				Sage::getInstance()->getScheduler()->scheduleRepeatingTask(new PearlingTask($player), 20);
				$entity = Entity::createEntity("EnderPearl", $player->getLevel(), new CompoundTag("", [
					"Pos" => new ListTag("Pos", [
						new DoubleTag("", $player->x),
						new DoubleTag("", $player->y+ $player->getEyeHeight()),
						new DoubleTag("", $player->z),
					]),
					"Motion" => new ListTag("Motion", [
						new DoubleTag("", -sin($player->yaw / 180 * M_PI) * cos($player->pitch / 180 * M_PI)),
						new DoubleTag("", -sin($player->pitch / 180 * M_PI)),
						new DoubleTag("", cos($player->yaw / 180 * M_PI) * cos($player->pitch / 180 * M_PI)),
					]),
					"Rotation" => new ListTag("Rotation", [
						new FloatTag("", $player->yaw),
						new FloatTag("", $player->pitch),
					]),
				]), $player);
				$entity->setMotion($entity->getMotion()->multiply(1));

				// $item->onClickAir($player, $player->getDirectionVector());
				return false;
			}else return false;
		}else return parent::onActivate($item, $player);
	}

}