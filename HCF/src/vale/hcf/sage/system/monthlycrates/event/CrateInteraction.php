<?php

declare(strict_types = 1);

#   /\ /\___ _ __ __ _ ___
#   \ \ / / _ \ '__/ _`|/_\
#    \ V / __/ | | (_| |__/
#     \_/ \___ |_| \__,|\___
#                  |___/

namespace vale\hcf\sage\system\monthlycrates\event;

//Base Libraries
use vale\hcf\sage\system\monthlycrates\inv\MonthlyCrateInventory;
use vale\hcf\sage\system\monthlycrates\util\CrateSounds;
use pocketmine\{Server, Player};
//Events
use pocketmine\event\Listener;
use pocketmine\event\player\{PlayerInteractEvent};
use pocketmine\event\inventory\{InventoryCloseEvent};
//Core
use vale\hcf\sage\system\monthlycrates\MonthlyCrates;
use vale\hcf\sage\Sage;
//Crate Inventory
use muqsit\invmenu\inventories\DoubleChestInventory;


class  CrateInteraction implements Listener {

	public function __construct(Sage $plugin){
		Server::getInstance()->getPluginManager()->registerEvents($this, $plugin);
	}

	public function onInteract(PlayerInteractEvent $event): void{
#variables
		$player = $event->getPlayer();
		$item = $player->getInventory()->getItemInHand();
		$echest = \pocketmine\item\Item::get(130, 0, 1);
		$echest->setCustomName("§r§l§b*§c*§b* §fJULY 2019 CC §l§b*§c*§b* §r§7(Right-Click)");
		#if(!in_array($player->getName(), MonthlyCrates::$opening)){
		if ($item->getId() == 130 && !$event->isCancelled()){
			switch($item->getName()) {
				case "§r§l§c*§f*§1* SAGE CRATE: §fJUNE §12021 §r§l§c*§f*§1* §r§7(Right-Click)":
					if(!in_array($player->getName(), MonthlyCrates::$opening)){
						$event->setCancelled();
						MonthlyCrateInventory::open($player, "july2019");
						CrateSounds::playSound($player, CrateSounds::NOTE_BD);
						array_push(MonthlyCrates::$opening, $player->getName());
						$item->setCount($item->getCount() - 1);
						$player->getInventory()->setItemInHand($item);
						MonthlyCrates::$opening[$player->getName()] = "july2019";
					}else{
						MonthlyCrateInventory::open($player, "july2019");
						$event->setCancelled();
						CrateSounds::playSound($player, CrateSounds::NOTE_BD);
						$item->setCount($item->getCount() - 1);
						$player->getInventory()->setItemInHand($item);
					}
					break;

			}
		}
		/**}else{
		$player->sendMessage("§l§c(!) §r§cYou cannot open another monthly crtae until the previous crate has been fully looted!");
		}**/
	}

	public function onClose(InventoryCloseEvent $event): void{
		$inv = $event->getInventory();
		$player = $event->getPlayer();
		if(in_array($player->getName(), MonthlyCrates::$opening) && $inv instanceof DoubleChestInventory) $player->addWindow($inv);
	}
}