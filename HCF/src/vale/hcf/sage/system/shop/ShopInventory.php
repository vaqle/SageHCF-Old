<?php

declare(strict_types = 1);

##CREDITS TO VERGE##

namespace vale\hcf\sage\system\shop;

use pocketmine\level\{Level, sound\AnvilFallSound, Position, particle\FloatingTextParticle};
use pocketmine\{Player, Server};

//Block & Item
use pocketmine\{
	block\Block, item\Item
};
use pocketmine\item\{enchantment\Enchantment, Sword, Tool, Armor};
//Custom Enchants Base
//nbts
use pocketmine\nbt\tag\{CompoundTag, IntTag, StringTag, ListTag};
//OTHER API's within Core
use pocketmine\math\Vector3;
//Event Libraries
use pocketmine\event\entity\{EntityDamageByEntityEvent, EntityDamageEvent};
use pocketmine\event\Listener;
use pocketmine\inventory\transaction\action\SlotChangeAction;
//InvMenu
use muqsit\invmenu\InvMenu;
use vale\hcf\sage\provider\DataProvider;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class ShopInventory{

	public $categories;
	public static $shelf = [];

	//documentation for setting up the items
	/*
	"Item name" => [item_id, item_damage, buy_price, sell_price]
	*/


	public static $buildingblocks = [
		"ICON" => ["§l§eBuilding Blocks\n§r§fClick to view this category",206,0, "Building_Blocks"],

		"§r§fGrass Block\n§r§f$3/ea" => [2, 0, 3, 0],
		"§r§fSpruce Wood\n§r§f$8/ea" => [17, 1, 8, 0],
		"§r§fBirch Wood\n§r§f$8/ea" => [17, 2, 8, 0],
		"§r§fJungle Wood\n§r§f$8/ea" => [17, 3, 8, 0],
		"§r§fAcacia Wood\n§r§f$8/ea" => [162, 0, 8, 0],
		"§r§fDark Oak Wood\n§r§f$8/ea" => [162, 1, 8, 0],
		"§r§fPodzol\n§r§f$11/ea" => [243, 0, 11, 0],
		"§r§fMycelium\n§r§f$10/ea" => [110, 0, 10, 0],
		"§r§fStone\n§r§f$5/ea" => [1, 0, 5, 0],
		"§r§fCobblestone\n§r§f$4/ea" => [4, 0, 4, 0],
		"§r§fStone Brick\n§r§f$6/ea" => [98, 0, 6, 0],
		"§r§fMossy Stone Brick\n§r§f$6/ea" => [98, 1, 6, 0],
		"§r§fCracked Stone Brick\n§r§f$6/ea" => [98, 2, 6, 0],
		"§r§fSandstone\n§r§f$12/ea" => [24, 0, 12, 0],
		"§r§fChiseled Sandstone\n§r§f$12/ea" => [24, 1, 12, 0],
		"§r§fSmooth Sandstone\n§r§f$48/ea" => [24, 2, 48, 0],
		"§r§fQuartz Block\n§r§f$12/ea" => [155, 0, 12, 0],
		"§r§fChiseled Quartz Block\n§r§f$48/ea" => [155, 1, 48, 0],
		"§r§fPillar Quartz Blocks\n§r§f$12/ea" => [155, 2, 12, 0],
		"§r§fNetherrack\n§r§f$2/ea" => [87, 0, 2, 0],
		"§r§fNether Brick\n§r§f$5/ea" => [112, 0, 0, 0],
		"§r§fSoul Sand\n§r§f$7/ea" => [88, 0, 7, 0],
		"§r§fEnd Stone\n§r§f$7/ea" => [206, 0, 7, 0],
		"§r§fGravel\n§r§f$3/ea" => [13, 0, 3, 0],
		"§r§fIce\n§r§f$50/ea" => [79, 0, 50, 0],
		"§r§fPacked Ice\n§r§f$75/ea" => [174, 0, 75, 0],
		"§r§fWood \n§r§f$0/ea" => [17, 0, 0, 0],
		"§r§fSnow Block\n§r§f$250/ea" => [80, 0, 250, 0],
		"§r§fClay\n§r§f$5/ea" => [337, 0, 5, 0],
		"§r§fClay Block\n§r§f$20/ea" => [82, 0, 20, 0],
		"§r§fStained Glass Block\n§r§f$50/ea" => [241, 0, 50, 0],
		"§r§fRed Stained Glass Block\n§r§f$50/ea" => [241, 14, 50, 0],
		"§r§fOrange Stained Glass Block\n§r§f$50/ea" => [241, 1, 50, 0],
		"§r§fMagneta Stained Glass Block\n§r§f$50/ea" => [241, 2, 50, 0],
		"§r§fLight Blue Stained Glass Block\n§r§f$50/ea" => [241, 3, 50, 0],
		"§r§fYellow Stained Glass Block\n§r§f$50/ea" => [241, 4, 50, 0],
		"§r§fLime Stained Glass Block\n§r§f$50/ea" => [241, 5, 50, 0],
		"§r§fPink Stained Glass Block\n§r§f$50/ea" => [241, 6, 50, 0],
	];


	public static function openMainShop(SagePlayer $player) : void{

		$menu = InvMenu::create(InvMenu::TYPE_CHEST);
		$menu->setName("BlockShop");
		$menu->readonly();
		$items = [
			"Block Shop" => [206, 0, 1, false],

		];

		foreach($items as $name => $data){

			($item = Item::get($data[0], $data[1], $data[2])
				->setCustomName("§r§l§e$name")
				->setLore(["§r§7Tap To Open The BlockShop"])
			);
			if($data[3]){
				$item->setNamedTagEntry(new ListTag("ench"));
			}
			$menu->getInventory()->addItem($item);
		}
		$menu->setListener( function(SagePlayer $player, Item $itemClicked, Item $itemClickedWith, SlotChangeAction $action){
			switch($itemClicked->getCustomName()){
				case "§r§l§eBlock Shop":
					$player->removeWindow($action->getInventory());
					self::openBlocksShop($player);
					break;
			}
		});

		$menu->send($player);

	}

	public static function openBlocksShop(SagePlayer $player) : void{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName("Building Blocks");
		$menu->readonly();
		$back = Item::get(Item::ARROW, 0, 1);
		$back->setCustomName("§r§l§cBack");
		$menu->getInventory()->setItem(53, $back);

		foreach(self::$buildingblocks as $name => $data){
			if($name !== "ICON"){
				$price = "$" . number_format($data[2]);
				$sell_price = $data[3] > 0 ? "$" . number_format($data[3]) : "§l§cNOT MARKETABLE";
				($pot = Item::get($data[0], $data[1], 1)
					->setCustomName($name)
					->setLore([
						"",
						"§r§e* Buy: §7$price",
						"§r§e* Sell: §7$sell_price",
						"§r§eRight-click to purchase this item",
					])
				);
				$menu->getInventory()->addItem($pot);

			}
		}
		$menu->setListener( function(SagePlayer $player, Item $itemClicked, Item $itemClickedWith, SlotChangeAction $action){

			$player->removeWindow($action->getInventory());
			if($itemClicked->getName() == "§r§l§cBack" && $itemClicked->getId() == Item::ARROW){
				$player->removeWindow($action->getInventory());
				Sage::getInstance()->getScheduler()->scheduleDelayedTask(new class($player) extends \pocketmine\scheduler\Task{
					public $player;
					public $menu;
					public function __construct(SagePlayer $player) {
						$this->player = $player;
					}

					public function onRun($tick){
						ShopInventory::openMainShop($this->player);
					}
				}, 10);

			}else{
				self::sendConfirmationMenu($player, $itemClicked->getId(), $itemClicked->getDamage());
			}


		});
		self::$shelf[$player->getName()] = "buildingblocks";
		Sage::getInstance()->getScheduler()->scheduleDelayedTask(new class($player, $menu) extends \pocketmine\scheduler\Task{
			public $player;
			public $menu;
			public function __construct(SagePlayer $player, $menu) {
				$this->player = $player;
				$this->menu = $menu;
			}

			public function onRun($tick){
				$this->menu->send($this->player);

			}
		}, 10);

	}

	public static function purchase(SagePlayer $player, int $id, int $meta, int $amount) : void{
		$categories = [self::$buildingblocks];
		$cs = self::$shelf[$player->getName()];
		foreach(self::$$cs as $name => $data){
			if($name !== "ICON"){
				if($data[0] === $id && $data[1] === $meta){
					$item = Item::get($data[0], $data[1], $amount);
					$price = $data[2] * $amount;
					if(Sage::getSQLProvider()->getBalance($player->getName()) >= $price){
						Sage::getSQLProvider()->reduceBalance($player->getName(), $price);
						$player->getInventory()->addItem($item);
					}else{
						$player->sendMessage("§r§c(!) §r§cYou lack sufficient funds to process this transaction");
					}
				}
			}
		}
	}

	public static function sendConfirmationMenu(SagePlayer $player, int $id, int $meta) : void{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName("Confirmation Menu");
		$menu->readonly();

		$cs = self::$shelf[$player->getName()];
		foreach(self::$$cs as $name => $data){
			if($name !== "ICON"){
				if($data[0] === $id && $data[1] === $meta){
					$price = $data[2];

				}
			}
		}

		$middleitem = $menu->getInventory()->getItem(31);
		$quantity = $middleitem->getCount();
		$finalprice = $price * $quantity;
		$balance =  Sage::getSQLProvider()->getBalance($player->getName()); 


		$menu->getInventory()->setItem(31, Item::get($id, $meta, 1));

		$menu->setListener( function(SagePlayer $player, Item $itemClicked, Item $itemClickedWith, SlotChangeAction $action) use($finalprice, $quantity){
			$middleitem = $action->getInventory()->getItem(31);
			$inv = $action->getInventory();
			if($itemClicked->getId() == 160){
				switch($itemClicked->getDamage()){
					case 13: //increase
						if(($middleitem->getCount() + $itemClicked->getCount()) <= 64){
							$middleitem->setCount($middleitem->getCount() + $itemClicked->getCount());
							$inv->setItem(31, $middleitem);
							$item = Item::get(355, 13, 1);
							$item->setCustomName("§r§l§aAccept");
							$item->setLore([
								"§r§eSelected Quantity: (§6$quantity)",
								"§r§eFinal Price: §6" . number_format($finalprice),
								"",
								"§r§7Right-Click to complete transaction(s)",
							]);
							$item->setNamedTagEntry(new ListTag("ench"));
							$inv->setItem(53, Item::get(Item::AIR));
							$inv->setItem(53, $item);
							#$player->sendMessage("counr increased");

						}
						break;
					case 14: //decrease
						if(($middleitem->getCount() - $itemClicked->getCount()) >= 1){
							$middleitem->setCount($middleitem->getCount() - $itemClicked->getCount());
							$inv->setItem(31, $middleitem);
							$item = Item::get(355, 13, 1);
							$item->setCustomName("§r§l§aAccept");
							$item->setLore([
								"§r§eSelected Quantity: (§6$quantity)",
								"§r§eFinal Price: §6" . number_format($finalprice),
								"",
								"§r§7Right-Click to complete transaction(s)",
							]);
							$item->setNamedTagEntry(new ListTag("ench"));
							$inv->setItem(53, $item);
							# $player->sendMessage("count decreased");

						}
						break;
				}
			}
			if($itemClicked->getId() == 355){
				switch($itemClicked->getDamage()){
					case 13:
						self::purchase($player, $middleitem->getId(), $middleitem->getDamage(), $middleitem->getCount());
						$inv->setItem(4, $inv->getItem(4));
						break;
					case 14:
						$player->removeWindow($action->getInventory());
						break;

				}
			}
		});


		($torch = Item::get(76, 0, 1)
			->setCustomName(" ")
		);
		$grayslots = [0, 1, 2, 3, 5, 6, 7, 8, 9, 17, 18, 26, 45, 46, 47, 48, 49, 50, 51, 52];
		foreach($grayslots as $slot){
			$menu->getInventory()->setItem($slot, ($item = Item::get(241, 7, 1)
				->setCustomName(" ")
			));
		}
		$item = Item::get(Item::PAINTING, 0, 1);
		$item->setCustomName("§r§6Your current balance:");
		$item->setLore(["§r§e$" . number_format($balance)]);
		$menu->getInventory()->setItem(4, $item);

		$item = Item::get(355, 14, 1);
		$item->setCustomName("§r§l§cCancel");
		$item->setLore(["§r§7Right-Click to cancel transaction(s)"]);
		$item->setNamedTagEntry(new ListTag("ench"));
		$menu->getInventory()->setItem(45, $item);

		$item = Item::get(355, 13, 1);
		$item->setCustomName("§r§l§aAccept");
		$item->setLore([
			"§r§eSelected Quantity: (§6$quantity)",
			"§r§eFinal Price: §6" . number_format($finalprice),
			"",
			"§r§7Right-Click to complete transaction(s)",
		]);
		$item->setNamedTagEntry(new ListTag("ench"));

		$menu->getInventory()->setItem(53, $item);
		$menu->getInventory()->setItem(22, $torch);
		$menu->getInventory()->setItem(40, $torch);
		foreach(range(27, 29) as $slot){
			$item = Item::get(160, 14, self::__translateSlotToQuantity($slot));
			$item->setCustomName("§r§cDecrease quantity (§4-" . self::__translateSlotToQuantity($slot) . "§c)");
			$item->setLore(["§r§7Right-Click to decrease purchase quantity"]);

			$menu->getInventory()->setItem($slot, $item);
		}

		foreach(range(33, 35) as $slot){
			$item = Item::get(160, 13, self::__translateSlotToQuantity($slot));
			$item->setCustomName("§r§aIncrease quantity (§2+" . self::__translateSlotToQuantity($slot) . "§a)");
			$item->setLore(["§r§7Right-Click to increase purchase quantity"]);

			$menu->getInventory()->setItem($slot, $item);
		}
		Sage::getInstance()->getScheduler()->scheduleDelayedTask(new class($player, $menu) extends \pocketmine\scheduler\Task{
			public $player;
			public $menu;
			public function __construct(SagePlayer $player, $menu) {
				$this->player = $player;
				$this->menu = $menu;
			}

			public function onRun($tick){
				$this->menu->send($this->player);
				Sage::playSound($this->player, "mob.villager.haggle", 0.9, 1, 2);
			}
		}, 10);

	}

	public static function __translateSlotToQuantity(int $slot) : int{
		switch($slot){
			case 27:
				return 64;
				break;
			case 28:
				return 10;
				break;
			case 29:
				return 1;
				break;
			case 33:
				return 1;
				break;
			case 34:
				return 10;
				break;
			case 35:
				return 64;
				break;
			default:
				return 5;
				break;


		}
	}
}



