<?php

namespace vale\hcf\sage\system\enchant;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;

class EnchantUtils
{

	public static function addEnchanmentToItem(Item $item, array $enchantments, int $level)
	{
		foreach ($enchantments as $enchantment) {
			$enchantment = Enchantment::getEnchantmentByName($enchantment);
			$item->addEnchantment(new EnchantmentInstance($enchantment, $level));
		}
	}
}
