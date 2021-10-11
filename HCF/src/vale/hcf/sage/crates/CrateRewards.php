<?php

namespace vale\hcf\sage\crates;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\level\particle\ItemBreakParticle;
use pocketmine\Server;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class CrateRewards{

	###GLOBAL REWARDS####
	public const VALUABLES = 0;
	public const LOWTIER_ARMOUR_LOOT_TABLE = 1;

	##PITEMS;
	public const PITEM_BONE = 999;


	public static function giveReward(SagePlayer $player, int $rewardID, int $amount, string $message = null)
	{
		switch ($rewardID) {
			case self::LOWTIER_ARMOUR_LOOT_TABLE;
			      $armourEnchants = [Enchantment::getEnchantment(Enchantment::PROTECTION),
					  Enchantment::getEnchantment(Enchantment::UNBREAKING)];
				$protection = Enchantment::getEnchantmentByName("Protection");
				$unbreaking = Enchantment::getEnchantmentByName("Unbreaking");
				$diamondhelemt = Item::get(Item::DIAMOND_HELMET);
				$diamondchest = Item::get(Item::DIAMOND_CHESTPLATE);
				  $rewards = [$diamondhelemt, $diamondchest];
				$reward = $rewards[array_rand($rewards)];
				$level = mt_rand(1,2);
				$reward->addEnchantment(new EnchantmentInstance($protection, $level));
				$reward->addEnchantment(new EnchantmentInstance($unbreaking, $level));
				$player->getInventory()->addItem($reward);
					$player->sendMessage($message);
					break;


		}
	}

}
