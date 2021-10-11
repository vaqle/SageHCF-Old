<?php

namespace vale\hcf\sage\models\util;


use muqsit\invmenu\InvMenu;
use muqsit\invmenu\MenuIds;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\Server;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class CrystalInventory
{

	public static function sendInv(SagePlayer $player)
	{
		$menu = InvMenu::create(MenuIds::TYPE_HOPPER);
		$menu->setName("§r§7Faction Crystal Merchant");
		$block = Item::get(Item::BONE, 1, 1);
		$block->setCustomName("§r§6Vales Exotic Bone");
		$block->setLore([
			'§r§7Hit a player with this to prevent them from building for §r§6§l8 seconds',
			'§r§c§lCOST: §r§e6 faction crystals'
		]);
		$block->getNamedTag()->setTag(new StringTag("test123"));
		$menu->getInventory()->setItem(0, $block);
		$menu->send($player);
		$menu->readonly(true);
		$menu->setListener(function (SagePlayer $player, Item $itemClicked, Item $itemClickedWith, SlotChangeAction $action) {
			if ($itemClicked->getId() == Item::BONE && $itemClicked->getDamage() == 1) {
				$faction = $player->getFaction();
				if (Sage::getFactionsManager()->getLeader($player->getFaction()) == $player->getName() || (Sage::getFactionsManager()->isOfficer($player->getFaction(), $player))) {
					if(Sage::getFactionsManager()->getCrystals($faction) >= 6) {
						Sage::getFactionsManager()->reduceCrystals($faction, 6);
						$members = Sage::getFactionsManager()->getMembers($faction);
						foreach ($members as $member) {
							$togive = Server::getInstance()->getPlayer($member);
							if ($togive instanceof SagePlayer) {
								if ($togive != null) {
									$block = Item::get(Item::BONE, 1, 1);
									$block->setCustomName("§r§6Vales Exotic Bone");
									$block->setLore([
										'§r§7Hit a player with this to prevent them from building for §r§6§l8 seconds'
									]);
									$togive->getInventory()->addItem($block);
									$togive->sendMessage("§r§7x1 §r§6§lExoticbone");
									$name = $togive . ", ";
									$player->sendMessage("§r§7" . $togive->getName() . " has recieved a §r§6§lExoticbone");

								}else{
									$player->sendMessage("§r§cInsufficient funds.");
								}
							}
						}
					}
				}
			}
		});
	}

	public static function sendPotInv(SagePlayer $player)
	{
		$menu = InvMenu::create(MenuIds::TYPE_HOPPER);
		$menu->setName("§e§l* NEW * §cPotion §r§cSpawners §r§e(Right-Click)");
		$block = Item::get(Item::OBSERVER, 1, 1);
		$block->setCustomName("§r§c§lPOTION SPAWNER §r§7(Right-Click) ");
		$block->setLore([
		    '',
			'',
			'§r§7Place this §c§lSpawner §r§7in your claim to generate unlimited potions!',
			'§r§7The §c§lspawner §r§7goes under a Chest!',
			'',
			'§r§a10,000$',
			'§r§7Pot Spawn Rate: (2) * (3)'
		]);
		$block->getNamedTag()->setTag(new StringTag("test"));
		$menu->getInventory()->setItem(0, $block);
		$menu->send($player);
		$menu->readonly(true);
		$menu->setListener(function (SagePlayer $player, Item $itemClicked, Item $itemClickedWith, SlotChangeAction $action) {
			if ($itemClicked->getId() == Item::OBSERVER && $itemClicked->getDamage() == 1) {
				if ($player->getBalance() >= 10000) {
					Sage::getSQLProvider()->reduceBalance($player->getName(),10000);
					$block = Item::get(Item::OBSERVER, 1, 1);
					$block->setCustomName("§r§c§lPOTION SPAWNER §r§7(Right-Click)");
					$block->setLore([
						'',
						'',
						'§r§7Place this §c§lSpawner §r§7in your claim to generate unlimited potions!',
						'§r§7The §c§lspawner §r§7goes under a Chest!',
						'',
						'§r§7Pot Spawn Rate: (2) * (3)'
					]);
					$player->getInventory()->addItem($block);
				}elseif ($player->getBalance() < 10000){
					$player->sendMessage("§r§cInsufficent Funds.");
				}
			}
		});
	}
}
