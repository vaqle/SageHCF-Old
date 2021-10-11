<?php

namespace vale\hcf\sage\models\util;


use muqsit\invmenu\InvMenu;
use muqsit\invmenu\MenuIds;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\BlockIds;
use pocketmine\block\StainedGlass;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;
use vale\hcf\sage\crates\CrateAPI;
use vale\hcf\sage\provider\DataProvider;
use vale\hcf\sage\SagePlayer;

class PartnerCrateInventory
{

	public array $slots = [];

	public $id;

	public $types = ["birdo", "vale"];

	/**
	 * @return array|mixed
	 */
	public function getSlots(): array
	{
		return $this->slots;
	}


	public function sendInv(SagePlayer $player)
	{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName("Preview");
		$menu->send($player);
	}

	public function sendPartnerCrateInventory(SagePlayer $player)
	{
		$menu = InvMenu::create(MenuIds::TYPE_DOUBLE_CHEST)
			->setName("§r§7Partner Crates");
		$birdo = Item::get(Item::MOB_HEAD, 1, 1);
		$birdo->setCustomName("§r§8§l[§r§3§lBirdos Crate §r§8§l]");
		$birdo->setLore([
			'§r§7You can redeem this key at the Santa',
			'§r§7NPC Located inside spawn',
			'',
			'§r§7Your keys' . DataProvider::getPartnerKeys($player->getName()),
			'§r§7Click to view rewards',
			'§r§7Tap with Key to redeem rewards'
		]);

		$ant = Item::get(Item::MOB_HEAD, 2, 1);
		$ant->setCustomName("§r§8§l[§r§e§lAnt's Crate §r§8§l]");
		$ant->setLore([
			'§r§7You can redeem this key at the Santa',
			'§r§7NPC Located inside spawn',
			'',
			'§r§7Your keys' . DataProvider::getPartnerKeys($player->getName()),
			'§r§7Click to view rewards',
			'§r§7Tap with Key to redeem rewards'
		]);

		$mysterycratekey = Item::get(Item::TRIPWIRE_HOOK, 0, 1);
		$mysterycratekey->setCustomName("§r§l§bPartner Crate Key Bundle §r§7(Right-Click)");
		$mysterycratekey->setLore([
			"§r§7Right-Click this item to receive a random Partner",
			"§r§bitem §7from the list! (§bx2)",
			"",
			"§r§l§bAVAILABLE PARTNERS",
			"§r§7* §fBirdos Lasso",
			"§r§7* §fAnts Focus Mode",
			"§r§7* §fMario's Anti Trap Beacon",
			"§r§7* §fNinja Star",
			"§r§7* §fCombos MedKit",
			"",
			"§r§l§cNOTE: §r§7Ensure that you have enough inventory",
			"§r§7slots before initiating item interaction!"
		]);
		$mysterycratekey->getNamedTag()->setTag(new StringTag("PartnerItem_Key"));
		$mysterycratekey->setNamedTagEntry(new ListTag("ench"));
		#$player->getInventory()->addItem($mysterycratekey);
		$slots = [0, 1, 9, 7, 17, 8, 52, 53, 44, 45, 46, 36];
		foreach ($slots as $slotId) {
			$menu->getInventory()->setItem($slotId, Item::get(Item::STAINED_GLASS, rand(1, 10), 1));
		}
		$menu->getInventory()->setItem(45, Item::get(Item::STAINED_GLASS, rand(1, 10), 1));
		$menu->getInventory()->setItem(11, $birdo);
		$menu->getInventory()->setItem(16, $ant);
		$menu->readonly(true);
		$menu->send($player);
		$menu->setListener(function (SagePlayer $player, Item $itemClicked, Item $itemClickedWith, SlotChangeAction $action) {
			if ($itemClicked->getCustomName() === "§r§8§l[§r§3§lBirdos Crate §r§8§l]") {
				if ($player->getPartnerKeys() >= 1) {
					$player->getLevel()->broadcastLevelSoundEvent(new Vector3($player->x, $player->y, $player->z), LevelSoundEventPacket::SOUND_TWINKLE);
					DataProvider::reducePartnerKeys($player->getName(), 1);
					CrateAPI::giveBirdoRewards($player);
					CrateAPI::giveBirdoRewards($player);
					CrateAPI::giveBirdoRewards($player);
				} elseif ($itemClicked->getCustomName() === "§r§8§l[§r§3§lBirdos Crate §r§8§l]" && DataProvider::getPartnerKeys($player->getName()) < 1) {
					$player->sendMessage("§l§c[!] §r§cNo Partner Keys were found for you “vaqle”\n§7You can buy these on our buycraft or keyalls!");
					$player->removeWindow($action->getInventory());
				}
			}
				if ($itemClicked->getCustomName() === "§r§8§l[§r§e§lAnt's Crate §r§8§l]") {
					if ($player->getPartnerKeys() >= 1) {
						$player->getLevel()->broadcastLevelSoundEvent(new Vector3($player->x, $player->y, $player->z), LevelSoundEventPacket::SOUND_TWINKLE);
						DataProvider::reducePartnerKeys($player->getName(), 1);
						CrateAPI::giveAntFocusModes($player);
						CrateAPI::giveAntFocusModes($player);
						CrateAPI::giveAntFocusModes($player);
					} elseif($itemClicked->getCustomName() === "§r§8§l[§r§e§lAnt's Crate §r§8§l]" && DataProvider::getPartnerKeys($player->getName()) < 1){
						$player->sendMessage("§l§c[!] §r§cNo Partner Keys were found for you “vaqle”\n§7You can buy these on our buycraft or keyalls!");
						$player->removeWindow($action->getInventory());
					}
				}
			});
	}
}

