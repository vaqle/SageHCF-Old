<?php
declare(strict_types=1);
namespace vale\hcf\sage\system\shop;

use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\system\shop\Shop;
use vale\hcf\sage\system\shop\ShopInventory;
use pocketmine\tile\Sign;
use pocketmine\{block\Block,
	block\BlockFactory,
	item\Item,
	item\ItemFactory,
	block\BlockIds,
	network\mcpe\protocol\UpdateBlockPacket,
	Player,
	scheduler\ClosureTask,
	utils\BinaryStream};
use pocketmine\math\Vector3;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as C;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockBreakEvent;

class ShopListener implements Listener {

	private $plugin;
	public function __construct(Sage $plugin){
		$this->plugin = $plugin;
	}

	/**
	 * @param SignChangeEvent $event
	 */
	public function onCreate(SignChangeEvent $event){
		$mgr = Sage::getShopManager();
		$player = $event->getPlayer();
		$sign = $event->getBlock();
		if($event->getLine(0) == "buy"){
			if(!$player->hasPermission("shop.create")) return;
			if($event->getLine(1) == "" || $event->getLine(2) == "" || $event->getLine(3) == "") return;
			$line = $event->getLine(1);
			$item = ItemFactory::fromString($line);
			$mgr->createShop($mgr::BUY, $item->getId(), $item->getDamage(), intval($event->getLine(2)), intval($event->getLine(3)), $sign->asVector3());
			$event->setLine(0, "§6§l* §r§e§lBUY §6§l*");
			$event->setLine(1, "§r§6" . $item->getName());
			$event->setLine(2, "§r§eQuanity: " . intval($event->getLine(2)));
			$event->setLine(3, "§r§a§l$" . intval($event->getLine(3)));
		}
		if($event->getLine(0) == "sell"){
			if(!$player->hasPermission("shop.create")) return;
			if($event->getLine(1) === "" || $event->getLine(2) === "" || $event->getLine(3) === "") return;
			$line = $event->getLine(1);
			$item = ItemFactory::fromString($line);
			$mgr->createShop($mgr::SELL, $item->getId(), $item->getDamage(), intval($event->getLine(2)), intval($event->getLine(3)), $sign->asVector3());
			$event->setLine(0, "§e§l* §r§6§lSELL §e§l*");
			$event->setLine(1, "§r§6" . $item->getName());
			$event->setLine(2, "§r§eQuanity: " . intval($event->getLine(2)));
			$event->setLine(3, "§r§a§l$" . intval($event->getLine(3)));
		}
	}

	/**
	 * @param BlockBreakEvent $event
	 */
	public function onBreak(BlockBreakEvent $event){
		$mgr = Sage::getShopManager();
		$vec = $event->getBlock()->asVector3();
		$player = $event->getPlayer();
		if($mgr->isShop($vec)){
			if(!$player->hasPermission("shop.destroy")) return;
			$mgr->moveShop($vec);
		}
	}

	/**
	 * @param PlayerInteractEvent $event
	 */
	public function onTap(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		$block = $event->getBlock();
		$vec = $block->asVector3();
		$mgr = Sage::getShopManager();
		if($player instanceof SagePlayer){
			if($block->getId() == BlockIds::SIGN_POST || $block->getId() == BlockIds::WALL_SIGN && $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK){
				if($mgr->isShop($vec)){
					$id = $mgr->getId($vec);
					$dmg = $mgr->getDamage($vec);
					$a = $mgr->getAmount($vec);
					$price = (int) $mgr->getPrice($vec);
					$inv = $player->getInventory();
					$item = Item::get($id, $dmg, $a);
					switch($mgr->getType($vec)){
						case $mgr::SELL:
							if($inv->contains($item)){
								Sage::getSQLProvider()->addMoney($player->getName(),$price);
								$inv->removeItem($item);
								Sage::playSound($player, "mob.villager.haggle", 0.9, 1, 2);
								$player->sendTip("§r§6§l* §r§eYou §r§e§lsuccesfully §r§esold that item.");
							} else $player->sendTip("§r§6§l* §r§eYou are trying to sell a item that you do not have.");
							break;

						case $mgr::BUY:
							if($player->getBalance() >= $price){
								Sage::getSQLProvider()->reduceBalance($player->getName(),$price);
								$inv->addItem($item);
								Sage::playSound($player, "mob.villager.haggle", 0.9, 1, 2);
								$player->sendTip("§r§6§l* §r§eYou succesfully §e§lpurchased §r§ethat item.");
							} else $player->sendTip("§r§e§l* §r§6§lINSUFFICIENT FUNDS.");
							break;
					}
				}
			}
		}
	}
}