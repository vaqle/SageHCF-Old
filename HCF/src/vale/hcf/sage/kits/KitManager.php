<?php

namespace vale\hcf\sage\kits;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\MenuIds;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\utils\Config;
use vale\hcf\sage\partneritems\PItemManager;
use vale\hcf\sage\provider\DataProvider;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\system\enchant\EnchantUtils;

class KitManager
{

	const ORBITALREPAIR = 108;
	const MAGMA = 405;
	const SPEED = 500;
	const NV = 601;

	public static function openKitMenu(SagePlayer $player)
	{
		$menu = InvMenu::create(MenuIds::TYPE_DOUBLE_CHEST);
		$menu->setName("§r§6§lSage HCF §r§7Gkits");
		$menu->readonly(true);

		$starterkit = Item::get(Item::CLOCK)->setCustomName("§r§f§lAesthete")->
		setLore([
			'§r§7The Aesthete Kit',
			'§r§7Keys and Items',
			'§r§7For first Joiners',
			'§r§6Cooldown: §r§e24 Hours',
			'§r§6Avaliable in:§r§e' . Sage::getTimeToFullString(DataProvider::$starterkit->get($player->getName())) ?? 0,

		]);
		$xp = (int) $player->getCurrentTotalXp();
		$player->setCurrentTotalXp($xp + (int) 123);
		$starterkit->getNamedTag()->setTag(new StringTag("starterkit"));
		$starterkit->setNamedTagEntry(new ListTag("ench"));
		$builderkit = Item::get(Item::GRASS)->setCustomName("§r§2§lBuilder")->
		setLore([
			'§r§7The Builder Kit',
			'§r§7Building Blocks',
			'§r§7For builders',
			'§r§6Cooldown: §r§e24 Hours',
			'§r§6Avaliable in:§r§e' . Sage::getTimeToFullString(DataProvider::$builderkit->get($player->getName())) ?? 0,

		]);
		$builderkit->getNamedTag()->setTag(new StringTag("builderkit"));
		$builderkit->setNamedTagEntry(new ListTag("ench"));
		$bardkit = Item::get(Item::GOLD_CHESTPLATE)->setCustomName("§r§e§lBard")->
		setLore([
			'§r§7A fully enchanted Bard Kit',
			'§r§7Used for Team Fights',
			'§r§7Has Ultimate Ability',
			'§r§6Cooldown: §r§e24 Hours',
			'§r§6Avaliable in:§r§e' . Sage::getTimeToFullString(DataProvider::$bardkit->get($player->getName())) ?? 0,

		]);
		$bardkit->getNamedTag()->setTag(new StringTag("bardkit"));
		$bardkit->setNamedTagEntry(new ListTag("ench"));
		$masterKit = Item::get(Item::NETHER_STAR)->setCustomName("§r§5§lMaster")->
		setLore([
			'§r§7A fully enchanted Protection Kit',
			'§r§7with §r§5§lcustom enchants',
			'§r§7contains §r§5Speed II §r§7, Night Vision I',
			'§r§6Cooldown: §r§e24 Hours',
			'§r§6Avaliable in:§r§e' . Sage::getTimeToFullString(DataProvider::$masterkit->get($player->getName())) ?? 0,

		]);
		$masterKit->getNamedTag()->setTag(new StringTag("masterkit"));
		$masterKit->setNamedTagEntry(new ListTag("ench"));
		$diamondKit = Item::get(Item::DIAMOND_CHESTPLATE)->setCustomName("§r§b§lDiamond")->
		setLore([
			'§r§7A fully enchanted Protection 2 Map kit',
			'§r§7for a melee damage player',
			'§r§6Cooldown: §r§e24 Hours',
			'§r§6Avaliable in:§r§e' . Sage::getTimeToFullString(DataProvider::$diamondkit->get($player->getName())) ?? 0,
		]);
		$diamondKit->getNamedTag()->setTag(new StringTag("diamondkit"));
		$diamondKit->setNamedTagEntry(new ListTag("ench"));

		$minerkit = Item::get(Item::IRON_CHESTPLATE)->setCustomName("§r§f§lMiner")->
		setLore([
			'§r§7The miner kit / class',
			'§r§7Used to mine out bases and mine',
			'§r§6Cooldown: §r§e24 Hours',
			'§r§6Avaliable in:§r§e' . Sage::getTimeToFullString(DataProvider::$minerkit->get($player->getName())) ?? 0,
		]);
		$minerkit->getNamedTag()->setTag(new StringTag("minerkit"));
		$minerkit->getNamedTag()->setTag(new ListTag("ench"));

		$pots = Item::get(Item::SPLASH_POTION,22,1)->setCustomName("§r§c§lPOTS")->
		setLore([
			'§r§7The pot kit / class',
			'§r§7Used to refill potions',
			'§r§6Cooldown: §r§e24 Hours',
			'§r§6Avaliable in:§r§e'
		]);
		$pots->getNamedTag()->setTag(new StringTag("potkit"));
		$pots->getNamedTag()->setTag(new ListTag("ench"));

		$menu->getInventory()->setItem(12, $masterKit);
		$menu->getInventory()->setItem(13, $diamondKit);
		$menu->getInventory()->setItem(14, $minerkit);
		$menu->getInventory()->setItem(22, $bardkit);
		$menu->getInventory()->setItem(31, $starterkit);
		$menu->getInventory()->setItem(40, $builderkit);
		$menu->getInventory()->setItem(50, $pots);
		$menu->send($player);
		$menu->setListener(function (SagePlayer $player, Item $itemClicked, Item $itemClickedWith, SlotChangeAction $action) {
			if ($player->hasPermission("hcf.kits.core.master") && $itemClicked->getCustomName() === "§r§5§lMaster" && $itemClicked->getNamedTag()->hasTag("masterkit")) {
				if (DataProvider::$masterkit->exists($player->getName())) {
					$player->sendMessage("§6§lSageHCF§r§7 | §r§eThe kit is currently under cooldown for\n§e" . self::secondsToTime(DataProvider::$masterkit->get($player->getName()) - time()));
				} else {
					$time = time() + 43200;
					DataProvider::$masterkit->set($player->getName(), $time);
					DataProvider::$masterkit->save();
					self::giveKit($player, "Master");
					$player->sendMessage("§6§lSageHCF§r§7 | §r§eYou have succesfully claimed your §5§lMaster Kit§r§e.");
				}
			}

			if ($itemClicked->getCustomName() === "§r§f§lAesthete" && $itemClicked->getNamedTag()->hasTag("starterkit")) {
				if (DataProvider::$starterkit->exists($player->getName())) {
					$player->sendMessage("§6§lSageHCF§r§7 | §r§eThe kit is currently under cooldown for\n§e" . self::secondsToTime(DataProvider::$starterkit->get($player->getName()) - time()));
				} else {
					$time = time() + 3600;
					DataProvider::$starterkit->set($player->getName(), $time);
					DataProvider::$starterkit->save();
					self::giveKit($player, "Starter");
					$player->sendMessage("§6§lSageHCF§r§7 | §r§eYou have succesfully claimed your §c§fAesthete Kit§r§e.");
				}
			}



			if ($itemClicked->getCustomName() === "§r§c§lPOTS" && $itemClicked->getNamedTag()->hasTag("potkit")) {
				if (DataProvider::$potkit->exists($player->getName())) {
					$player->sendMessage("§6§lSageHCF§r§7 | §r§eThe kit is currently under cooldown for\n§e");
				} else {
					$time = time() + 500;
					DataProvider::$potkit->set($player->getName(), $time);
					DataProvider::$potkit->save();
					self::giveKit($player, "Pot");
					$player->sendMessage("§6§lSageHCF§r§7 | §r§eYou have succesfully claimed your §c§lPot Kit§r§e.");
				}
			}


			if ($player->hasPermission("hcf.kits.core.diamond") && $itemClicked->getCustomName() === "§r§b§lDiamond" && $itemClicked->getNamedTag()->hasTag("diamondkit")) {
				if (DataProvider::$diamondkit->exists($player->getName())) {
					$player->sendMessage("§6§lSageHCF§r§7 | §r§eThe kit is currently under cooldown for\n§e" . self::secondsToTime(DataProvider::$diamondkit->get($player->getName()) - time()));
				} else {
					$time = time() + 43200;
					DataProvider::$diamondkit->set($player->getName(), $time);
					DataProvider::$diamondkit->save();
					self::giveKit($player, "Diamond");
					$player->sendMessage("§6§lSageHCF§r§7 | §r§eYou have succesfully claimed your §b§lDiamond Kit§r§e.");
				}
			}

			if ($player->hasPermission("hcf.kits.core.builder") && $itemClicked->getNamedTag()->hasTag("builderkit")) {
				if (DataProvider::$builderkit->exists($player->getName())) {
					$player->sendMessage("§6§lSageHCF§r§7 | §r§eThe kit is currently under cooldown for\n§e" . self::secondsToTime(DataProvider::$builderkit->get($player->getName()) - time()));
				} else {
					$time = time() + 43200;
					DataProvider::$builderkit->set($player->getName(), $time);
					DataProvider::$builderkit->save();
					self::giveKit($player, "Builder");
					$player->sendMessage("§6§lSageHCF§r§7 | §r§eYou have succesfully claimed your §2§lBuilder Kit§r§e.");
				}
			}

			if ($player->hasPermission("hcf.kits.core.bard") && $itemClicked->getNamedTag()->hasTag("bardkit")) {
				if (DataProvider::$bardkit->exists($player->getName())) {
					$player->sendMessage("§6§lSageHCF§r§7 | §r§eThe kit is currently under cooldown for\n§e" . self::secondsToTime(DataProvider::$bardkit->get($player->getName()) - time()));
				} else {
					$time = time() + 43200;
					DataProvider::$bardkit->set($player->getName(), $time);
					DataProvider::$bardkit->save();
					self::giveKit($player, "Bard");
					$player->sendMessage("§6§lSageHCF§r§7 | §r§eYou have succesfully claimed your §e§lBard Kit§r§e.");
				}
			}

			if ($player->hasPermission("hcf.kits.core.miner") &&  $itemClicked->getNamedTag()->hasTag("minerkit")) {
				if (DataProvider::$minerkit->exists($player->getName())) {
					$player->sendMessage("§6§lSageHCF§r§7 | §r§eThe kit is currently under cooldown for\n§e" . self::secondsToTime(DataProvider::$minerkit->get($player->getName()) - time()));
				} else {
					$time = time() + 43200;
					DataProvider::$minerkit->set($player->getName(), $time);
					DataProvider::$minerkit->save();
					self::giveKit($player, "Miner");
				}
			}
		});
	}


	public static function secondsToTime(int $secs)
	{
#Variables
		$s = $secs % 60;
		$m = floor(($secs % 3600) / 60);
		$h = floor(($secs % 86400) / 3600);
		$d = floor(($secs % 2592000) / 86400);
		$M = floor($secs / 2592000);

		return "$d days $h hours $m minutes $s seconds";
	}


	public static function processCooldown(): void
	{
		$kits = [DataProvider::$masterkit, DataProvider::$minerkit, DataProvider::$diamondkit, DataProvider::$bardkit, DataProvider::$starterkit,DataProvider::$builderkit,DataProvider::$potkit];
		foreach ($kits as $kit) {
			foreach ($kit->getAll() as $player => $time) {
				if (time() > $time) {
					$kit->remove($player);
					$kit->save();
				}
			}
		}
	}



	public static function giveKit(SagePlayer $player, string $type)
	{
		switch ($type) {

			case "Pot":

				for ($i = 0; $i < 12; $i++) {
					$nv = Item::get(373, 6, 1);
					if ($player->getInventory()->canAddItem($nv)) {
						$player->getInventory()->addItem($nv);
					} else $player->getLevel()->dropItem($player, $nv);
				}

				for ($i = 0; $i < 12; $i++) {
					$speed = Item::get(373, 15, 1);
					if ($player->getInventory()->canAddItem($speed)) {
						$player->getInventory()->addItem($speed);
					} else $player->getLevel()->dropItem($player, $speed);
				}

				break;

			case "King":
				$player->getArmorInventory()->clearAll();
				$player->getInventory()->clearAll();
				$helmet = Item::get(Item::DIAMOND_HELMET, 0, 1);
				$helmet->setCustomName("§r§6King §r§7Helmet");
				$helmet->setLore([
					'§r§4Night Vision II',
				]);
				$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));


				$chestplate = Item::get(Item::DIAMOND_CHESTPLATE, 0, 1);
				$chestplate->setCustomName("§r§6King §r§7Chestplate");
				$chestplate->setLore([
					'§r§4Magma II',
					'§r§4Repair II',
				]);
				$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));



				$leggs = Item::get(Item::DIAMOND_LEGGINGS, 0, 1);
				$leggs->setCustomName("§r§6King §r§7Leggings");
				$leggs->setLore([
					'§r§4Repair II',
				]);
				$leggs->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$leggs->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));


				$boots = Item::get(Item::DIAMOND_BOOTS, 0, 1);
				$boots->setCustomName("§r§6King §r§7Boots");
				$boots->setLore([
					'§r§4Speed II',
				]);

				$sword = Item::get(Item::DIAMOND_SWORD);
				$sword->setCustomName("§r§6King §r§7Sword");
				$sword->setLore([
					'§r§4Headless II',
				]);

				$pearls = Item::get(Item::ENDER_PEARL, 0, 16);
				$carrots = Item::get(Item::GOLDEN_CARROT, 0, 32);
				$gapples = Item::get(Item::GOLDEN_APPLE, 0 ,64);
				$player->addEffect(new EffectInstance(Effect::getEffectByName("Strength"),999999,3));
				$sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 2));
				$sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 1));
				$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));


				if ($player->getInventory()->canAddItem($sword)) {
					$player->getInventory()->addItem($sword);
				} else {
					$player->getLevel()->dropItem($player, $sword);
				}

				if ($player->getInventory()->canAddItem($gapples)) {
					$player->getInventory()->addItem($gapples);
				} else {
					$player->getLevel()->dropItem($player, $gapples);
				}


				if ($player->getInventory()->canAddItem($pearls)) {
					$player->getInventory()->addItem($pearls);
				} else {
					$player->getLevel()->dropItem($player, $pearls);
				}

				if ($player->getInventory()->canAddItem($carrots)) {
					$player->getInventory()->addItem($carrots);
				} else {
					$player->getLevel()->dropItem($player, $carrots);
				}

				if ($player->getArmorInventory()->canAddItem($helmet)) {
					$player->getArmorInventory()->setHelmet($helmet);
				} else {
					$player->getLevel()->dropItem($player, $helmet);
				}
				if ($player->getArmorInventory()->canAddItem($chestplate)) {
					$player->getArmorInventory()->setChestplate($chestplate);
				} else {
					$player->getLevel()->dropItem($player, $chestplate);
				}
				if ($player->getArmorInventory()->canAddItem($leggs)) {
					$player->getArmorInventory()->setLeggings($leggs);
				} else {
					$player->getLevel()->dropItem($player, $leggs);
				}
				if ($player->getArmorInventory()->canAddItem($boots)) {
					$player->getArmorInventory()->setBoots($boots);
				} else {
					$player->getLevel()->dropItem($player, $boots);
				}
				for ($i = 0; $i < 32; $i++) {
					$healing = Item::get(Item::SPLASH_POTION, 22, 1);
					if ($player->getInventory()->canAddItem($healing)) {
						$player->getInventory()->addItem($healing);
					} else $player->getLevel()->dropItem($player, $healing);
				}
				break;



			case "Master":
				$helmet = Item::get(Item::DIAMOND_HELMET, 0, 1);
				$helmet->setCustomName("§r§5Master §r§7Helmet");
				$helmet->setLore([
					'§r§4Night Vision II',
				]);
				$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));


				$chestplate = Item::get(Item::DIAMOND_CHESTPLATE, 0, 1);
				$chestplate->setCustomName("§r§5Master §r§7Chestplate");
				$chestplate->setLore([
					'§r§4Magma II',
					'§r§4Repair II',
				]);
				$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));



				$leggs = Item::get(Item::DIAMOND_LEGGINGS, 0, 1);
				$leggs->setCustomName("§r§5Master §r§7Leggings");
				$leggs->setLore([
					'§r§4Repair II',
				]);
				$leggs->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$leggs->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
                


				$boots = Item::get(Item::DIAMOND_BOOTS, 0, 1);
				$boots->setCustomName("§r§5Master §r§7Boots");
				$boots->setLore([
					'§r§4Speed II',
				]);

				$sword = Item::get(Item::DIAMOND_SWORD);
				$sword->setCustomName("§r§5Master §r§7Sword");
				$sword->setLore([
					'§r§4Headless II',
				]);

				$pearls = Item::get(Item::ENDER_PEARL, 0, 16);
				$carrots = Item::get(Item::GOLDEN_CARROT, 0, 32);
				$sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 2));
				$sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 1));
				$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
                	$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::FEATHER_FALLING), 2));

				if ($player->getInventory()->canAddItem($sword)) {
					$player->getInventory()->addItem($sword);
				} else {
					$player->getLevel()->dropItem($player, $sword);
				}

				if ($player->getInventory()->canAddItem($pearls)) {
					$player->getInventory()->addItem($pearls);
				} else {
					$player->getLevel()->dropItem($player, $pearls);
				}

				if ($player->getInventory()->canAddItem($carrots)) {
					$player->getInventory()->addItem($carrots);
				} else {
					$player->getLevel()->dropItem($player, $carrots);
				}

				if ($player->getArmorInventory()->canAddItem($helmet)) {
					$player->getArmorInventory()->setHelmet($helmet);
				} else {
					$player->getLevel()->dropItem($player, $helmet);
				}
				if ($player->getArmorInventory()->canAddItem($chestplate)) {
					$player->getArmorInventory()->setChestplate($chestplate);
				} else {
					$player->getLevel()->dropItem($player, $chestplate);
				}
				if ($player->getArmorInventory()->canAddItem($leggs)) {
					$player->getArmorInventory()->setLeggings($leggs);
				} else {
					$player->getLevel()->dropItem($player, $leggs);
				}
				if ($player->getArmorInventory()->canAddItem($boots)) {
					$player->getArmorInventory()->setBoots($boots);
				} else {
					$player->getLevel()->dropItem($player, $boots);
				}
				for ($i = 0; $i < 32; $i++) {
					$healing = Item::get(Item::SPLASH_POTION, 22, 1);
					if ($player->getInventory()->canAddItem($healing)) {
						$player->getInventory()->addItem($healing);
					} else $player->getLevel()->dropItem($player, $healing);
				}
				break;

			case "deatban":
				$helmet = Item::get(Item::DIAMOND_HELMET, 0, 1);
				$helmet->setCustomName("§r§5Master §r§7Helmet");
				$helmet->setLore([
					'§r§4Night Vision II',
				]);
				$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
				$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(self::NV), 1));
				$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(self::ORBITALREPAIR), 1));

				$chestplate = Item::get(Item::DIAMOND_CHESTPLATE, 0, 1);
				$chestplate->setCustomName("§r§5Master §r§7Chestplate");
				$chestplate->setLore([
					'§r§4Magma II',
					'§r§4Repair II',
				]);
				$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));



				$leggs = Item::get(Item::DIAMOND_LEGGINGS, 0, 1);
				$leggs->setCustomName("§r§5Master §r§7Leggings");
				$leggs->setLore([
					'§r§4Repair II',
				]);
				$leggs->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$leggs->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));

				$boots = Item::get(Item::DIAMOND_BOOTS, 0, 1);
				$boots->setCustomName("§r§5Master §r§7Boots");
				$boots->setLore([
					'§r§4Speed II',
				]);

				$sword = Item::get(Item::DIAMOND_SWORD);
				$sword->setCustomName("§r§5Master §r§7Sword");
				$sword->setLore([
					'§r§4Headless II',
				]);

				$pearls = Item::get(Item::ENDER_PEARL, 0, 16);
				$carrots = Item::get(Item::GOLDEN_CARROT, 0, 32);
				$sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 2));
				$sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 1));
				$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
               	$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::FEATHER_FALLING), 2));
				$player->getArmorInventory()->setHelmet($helmet);
				$player->getArmorInventory()->setChestplate($chestplate);
				$player->getArmorInventory()->setLeggings($leggs);
				$player->getArmorInventory()->setBoots($boots);
				$player->getInventory()->setItem(0, $sword);
				$player->getInventory()->setItem(1, $carrots);
				$player->getInventory()->addItem(Item::get(Item::SPLASH_POTION,22,45));

				break;

			case "Bard":
				$helmet = Item::get(Item::GOLD_HELMET, 0, 1);
				$helmet->setCustomName("§r§eBard §r§7Helmet");
				$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
				$chestplate = Item::get(Item::GOLD_CHESTPLATE, 0, 1);
				$chestplate->setCustomName("§r§eBard §r§7Chestplate");
				$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
				$leggs = Item::get(Item::GOLD_LEGGINGS, 0, 1);
				$leggs->setCustomName("§r§eBard §r§7Leggings");
				$leggs->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$leggs->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
				$boots = Item::get(Item::GOLD_BOOTS, 0, 1);
				$boots->setCustomName("§r§eBard §r§7Boots");
				$sword = Item::get(Item::DIAMOND_SWORD);
				$sword->setCustomName("§r§eBard §r§7Sword");
				$pearls = Item::get(Item::ENDER_PEARL, 0, 16);
				$carrots = Item::get(Item::GOLDEN_CARROT, 0, 32);
				$blaze =  Item::get(Item::BLAZE_POWDER, 0, 32);
				$dye = Item::get(Item::DYE, 0, 32);
				$sugar = Item::get(Item::SUGAR, 0, 32);
				$feather = Item::get(Item::FEATHER, 0, 32);
				$ghast = Item::get(Item::GHAST_TEAR, 0, 16);
				$iron = Item::get(Item::IRON_INGOT, 0, 16);
				$magma = Item::get(Item::MAGMA_CREAM, 0 ,8);
				$spidereye = Item::get(Item::SPIDER_EYE, 0 ,16);
				$sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 2));
				$sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 1));
				$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
                           	$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::FEATHER_FALLING), 2));

				if ($player->getInventory()->canAddItem($sword)) {
					$player->getInventory()->addItem($sword);
				} else {
					$player->getLevel()->dropItem($player, $sword);
				}

				if ($player->getInventory()->canAddItem($pearls)) {
					$player->getInventory()->addItem($pearls);
				} else {
					$player->getLevel()->dropItem($player, $pearls);
				}
				if ($player->getInventory()->canAddItem($blaze)) {
					$player->getInventory()->addItem($blaze);
				} else {
					$player->getLevel()->dropItem($player, $blaze);
				}
				if ($player->getInventory()->canAddItem($spidereye)) {
					$player->getInventory()->addItem($spidereye);
				} else {
					$player->getLevel()->dropItem($player, $spidereye);
				}
				if ($player->getInventory()->canAddItem($feather)) {
					$player->getInventory()->addItem($feather);
				} else {
					$player->getLevel()->dropItem($player, $feather);
				}
				if ($player->getInventory()->canAddItem($magma)) {
					$player->getInventory()->addItem($magma);
				} else {
					$player->getLevel()->dropItem($player, $magma);
				}
				if ($player->getInventory()->canAddItem($iron)) {
					$player->getInventory()->addItem($iron);
				} else {
					$player->getLevel()->dropItem($player, $iron);
				}
				if ($player->getInventory()->canAddItem($dye)) {
					$player->getInventory()->addItem($dye);
				} else {
					$player->getLevel()->dropItem($player, $dye);
				}
				if ($player->getInventory()->canAddItem($sugar)) {
					$player->getInventory()->addItem($sugar);
				} else {
					$player->getLevel()->dropItem($player, $sugar);
				}
				if ($player->getInventory()->canAddItem($ghast)) {
					$player->getInventory()->addItem($ghast);
				} else {
					$player->getLevel()->dropItem($player, $ghast);
				}

				if ($player->getInventory()->canAddItem($carrots)) {
					$player->getInventory()->addItem($carrots);
				} else {
					$player->getLevel()->dropItem($player, $carrots);
				}

				if ($player->getArmorInventory()->canAddItem($helmet)) {
					$player->getArmorInventory()->setHelmet($helmet);
				} else {
					$player->getLevel()->dropItem($player, $helmet);
				}
				if ($player->getArmorInventory()->canAddItem($chestplate)) {
					$player->getArmorInventory()->setChestplate($chestplate);
				} else {
					$player->getLevel()->dropItem($player, $chestplate);
				}
				if ($player->getArmorInventory()->canAddItem($leggs)) {
					$player->getArmorInventory()->setLeggings($leggs);
				} else {
					$player->getLevel()->dropItem($player, $leggs);
				}
				if ($player->getArmorInventory()->canAddItem($boots)) {
					$player->getArmorInventory()->setBoots($boots);
				} else {
					$player->getLevel()->dropItem($player, $boots);
				}

				for ($i = 0; $i < 32; $i++) {
					$healing = Item::get(Item::SPLASH_POTION, 22, 1);
					if ($player->getInventory()->canAddItem($healing)) {
						$player->getInventory()->addItem($healing);
					} else $player->getLevel()->dropItem($player, $healing);
				}
				break;

			case "Builder":
				$helmet = Item::get(Item::IRON_HELMET, 0, 1);
				$helmet->setCustomName("§r§fBuilder§r§7Helmet");
				$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
				$chestplate = Item::get(Item::IRON_CHESTPLATE, 0, 1);
				$chestplate->setCustomName("§r§fBuilder §r§7Chestplate");
				$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
				$leggs = Item::get(Item::IRON_LEGGINGS, 0, 1);
				$leggs->setCustomName("§r§fBuilder §r§7Leggings");
				$leggs->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$leggs->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
				$boots = Item::get(Item::IRON_BOOTS, 0, 1);
				$boots->setCustomName("§r§fBuilder §r§7Boots");
				$wood = Item::get(Item::WOOD, 0 ,64);
				$stone = Item::get(Item::STONE_BRICK, 0 ,255);
				$glass = Item::get(Item::GLASS, 0 ,255);
				$torch = Item::get(Item::TORCH, 0 ,64);
				$fence = Item::get(Item::FENCE_GATE, 0 ,64);
				$sign =  Item::get(Item::SIGN,0,16);
				$bucket1 = Item::get(Item::WATER, 0 ,1);
				$chest = Item::get(Item::CHEST, 0,64);

				if ($player->getInventory()->canAddItem($wood)) {
					$player->getInventory()->addItem($wood);
				} else {
					$player->getLevel()->dropItem($player, $wood);
				}
				if ($player->getInventory()->canAddItem($bucket1)) {
					$player->getInventory()->addItem($bucket1);
				} else {
					$player->getLevel()->dropItem($player, $bucket1);
				}
				if ($player->getInventory()->canAddItem($chest)) {
					$player->getInventory()->addItem($chest);
				} else {
					$player->getLevel()->dropItem($player, $chest);
				}
				if ($player->getInventory()->canAddItem($sign)) {
					$player->getInventory()->addItem($sign);
				} else {
					$player->getLevel()->dropItem($player, $sign);
				}

				if ($player->getInventory()->canAddItem($stone)) {
					$player->getInventory()->addItem($stone);
				} else {
					$player->getLevel()->dropItem($player, $stone);
				}
				if ($player->getInventory()->canAddItem($glass)) {
					$player->getInventory()->addItem($glass);
				} else {
					$player->getLevel()->dropItem($player, $glass);
				}
				if ($player->getInventory()->canAddItem($torch)) {
					$player->getInventory()->addItem($torch);
				} else {
					$player->getLevel()->dropItem($player, $torch);
				}
				if ($player->getInventory()->canAddItem($fence)) {
					$player->getInventory()->addItem($fence);
				} else {
					$player->getLevel()->dropItem($player, $fence);
				}

				if ($player->getArmorInventory()->canAddItem($helmet)) {
					$player->getArmorInventory()->setHelmet($helmet);
				} else {
					$player->getLevel()->dropItem($player, $helmet);
				}
				if ($player->getArmorInventory()->canAddItem($chestplate)) {
					$player->getArmorInventory()->setChestplate($chestplate);
				} else {
					$player->getLevel()->dropItem($player, $chestplate);
				}
				if ($player->getArmorInventory()->canAddItem($leggs)) {
					$player->getArmorInventory()->setLeggings($leggs);
				} else {
					$player->getLevel()->dropItem($player, $leggs);
				}
				if ($player->getArmorInventory()->canAddItem($boots)) {
					$player->getArmorInventory()->setBoots($boots);
				} else {
					$player->getLevel()->dropItem($player, $boots);
				}
				break;

			case "Starter":
				$key1 = Item::get(Item::DYE, 9, 5);
				$key1->setCustomName("§r§d§lHaze §r§7Key (Right-Click)");
				$key1->setLore([
					"§r§7Right-Click this key on the §l§dHaze",
					"§r§l§dCrate §r§7located at §dspawn §7to obtain rewards.",
					"§r",
					"§r§dstore.buycraft.net"
				]);
				$key1->setNamedTagEntry(new ListTag("ench"));
				$key2 = Item::get(Item::TRIPWIRE_HOOK, 0, 3);
				$key2->setCustomName("§r§c§lAbility §r§7Key (Right-Click)");
				$key2->setLore([
					"§r§7Right-Click this key on the §l§cAbility",
					"§r§l§cCrate §r§7located at §cspawn §7to obtain rewards.",
					"§r",
					"§r§cstore.buycraft.net"
				]);
				$key2->setNamedTagEntry(new ListTag("ench"));
				$key2->getNamedTag()->setTag(new StringTag("abilitykeyxd"));
				$lol = Item::get(Item::DISPENSER, 0, 1);
				$lol->setCustomName("§r§l§f*§b*§f*§r§bAir Drop§r§l§f*§b*§f*");
				$lol->setLore([
					'§r§7a unique cache of equipment that can be won from crates',
					'§r§7or bought from our buycraft',
					'§r§7((This can only be placed in your factions territory or wilderness)',
					'',
					'§r§7Winning §r§b§lRanks §r§7is possible from an §r§b§lAirDrop.',
					'',
					'§r§b§o§lshop.hcf.net',
				]);
				$lol->getNamedTag()->setTag(new StringTag("REWARD_AIRDROP"));
				$food = Item::get(Item::COOKED_BEEF, 0, 32);
				$e4pick = Item::get(Item::DIAMOND_PICKAXE, 0,1);
				$e4pick->setCustomName("§r§fAesthete §r§7Pick");
				$e4pick->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY),4));
				if ($player->getInventory()->canAddItem($food)) {
					$player->getInventory()->addItem($food);
				} else {
					$player->getLevel()->dropItem($player, $food);
				}
				if ($player->getInventory()->canAddItem($e4pick)) {
					$player->getInventory()->addItem($e4pick);
				} else {
					$player->getLevel()->dropItem($player, $e4pick);
				}
				if ($player->getInventory()->canAddItem($key1)) {
					$player->getInventory()->addItem($key1);
				} else {
					$player->getLevel()->dropItem($player, $key1);
				}
				if ($player->getInventory()->canAddItem($key2)) {
					$player->getInventory()->addItem($key2);
				} else {
					$player->getLevel()->dropItem($player, $key2);
				}
				if ($player->getInventory()->canAddItem($lol)) {
					$player->getInventory()->addItem($lol);
				} else {
					$player->getLevel()->dropItem($player, $lol);
				}
				break;

			case "Diamond":
				$helmet = Item::get(Item::DIAMOND_HELMET, 0, 1);
				$helmet->setCustomName("§r§bDiamond §r§7Helmet");
				$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
				$chestplate = Item::get(Item::DIAMOND_CHESTPLATE, 0, 1);
				$chestplate->setCustomName("§r§bDiamond §r§7Chestplate");
				$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
				$leggs = Item::get(Item::DIAMOND_LEGGINGS, 0, 1);
				$leggs->setCustomName("§r§bDiamond §r§7Leggings");
				$leggs->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$leggs->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
				$boots = Item::get(Item::DIAMOND_BOOTS, 0, 1);
				$boots->setCustomName("§r§bDiamond §r§7Boots");
				$sword = Item::get(Item::DIAMOND_SWORD);
				$sword->setCustomName("§r§bDiamond §r§7Sword");
				$pearls = Item::get(Item::ENDER_PEARL, 0, 16);
				$carrots = Item::get(Item::GOLDEN_CARROT, 0, 32);
				$sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 2));
				$sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 1));
				$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
				if ($player->getInventory()->canAddItem($sword)) {
					$player->getInventory()->addItem($sword);
				} else {
					$player->getLevel()->dropItem($player, $sword);
				}

				if ($player->getInventory()->canAddItem($pearls)) {
					$player->getInventory()->addItem($pearls);
				} else {
					$player->getLevel()->dropItem($player, $pearls);
				}

				if ($player->getInventory()->canAddItem($carrots)) {
					$player->getInventory()->addItem($carrots);
				} else {
					$player->getLevel()->dropItem($player, $carrots);
				}

				if ($player->getArmorInventory()->canAddItem($helmet)) {
					$player->getArmorInventory()->setHelmet($helmet);
				} else {
					$player->getLevel()->dropItem($player, $helmet);
				}
				if ($player->getArmorInventory()->canAddItem($chestplate)) {
					$player->getArmorInventory()->setChestplate($chestplate);
				} else {
					$player->getLevel()->dropItem($player, $chestplate);
				}
				if ($player->getArmorInventory()->canAddItem($leggs)) {
					$player->getArmorInventory()->setLeggings($leggs);
				} else {
					$player->getLevel()->dropItem($player, $leggs);
				}
				if ($player->getArmorInventory()->canAddItem($boots)) {
					$player->getArmorInventory()->setBoots($boots);
				} else {
					$player->getLevel()->dropItem($player, $boots);
				}
				for ($i = 0; $i < 32; $i++) {
					$healing = Item::get(Item::SPLASH_POTION, 22, 1);
					if ($player->getInventory()->canAddItem($healing)) {
						$player->getInventory()->addItem($healing);
					} else $player->getLevel()->dropItem($player, $healing);
				}
				break;

			case "Miner":
				$helmet = Item::get(Item::IRON_HELMET, 0, 1);
				$helmet->setCustomName("§r§fMiner §r§7Helmet");
				$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
				$chestplate = Item::get(Item::IRON_CHESTPLATE, 0, 1);
				$chestplate->setCustomName("§r§fMiner §r§7Chestplate");
				$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
				$leggs = Item::get(Item::IRON_LEGGINGS, 0, 1);
				$leggs->setCustomName("§r§fMiner §r§7Leggings");
				$leggs->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$leggs->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
				$boots = Item::get(Item::IRON_BOOTS, 0, 1);
				$boots->setCustomName("§r§fMiner §r§7Boots");
				$pearls = Item::get(Item::ENDER_PEARL, 0, 16);
				$carrots = Item::get(Item::GOLDEN_CARROT, 0, 32);
				$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
				$pick = Item::get(Item::DIAMOND_PICKAXE)->
				setCustomName("§r§fMiner §r§7Pickaxe");
				$pick->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY),4));
				$pick->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
				$craftingTable = Item::get(Item::CRAFTING_TABLE, 0 ,1);
				$furnace = Item::get(Item::FURNACE);

				if ($player->getInventory()->canAddItem($furnace)) {
					$player->getInventory()->addItem($furnace);
				} else {
					$player->getLevel()->dropItem($player, $furnace);
				}
				if ($player->getInventory()->canAddItem($craftingTable)) {
					$player->getInventory()->addItem($craftingTable);
				} else {
					$player->getLevel()->dropItem($player, $craftingTable);
				}
				if ($player->getInventory()->canAddItem($pick)) {
					$player->getInventory()->addItem($pick);
				} else {
					$player->getLevel()->dropItem($player, $pick);
				}
				if ($player->getInventory()->canAddItem($pearls)) {
					$player->getInventory()->addItem($pearls);
				} else {
					$player->getLevel()->dropItem($player, $pearls);
				}

				if ($player->getInventory()->canAddItem($carrots)) {
					$player->getInventory()->addItem($carrots);
				} else {
					$player->getLevel()->dropItem($player, $carrots);
				}

				if ($player->getArmorInventory()->canAddItem($helmet)) {
					$player->getArmorInventory()->setHelmet($helmet);
				} else {
					$player->getLevel()->dropItem($player, $helmet);
				}
				if ($player->getArmorInventory()->canAddItem($chestplate)) {
					$player->getArmorInventory()->setChestplate($chestplate);
				} else {
					$player->getLevel()->dropItem($player, $chestplate);
				}
				if ($player->getArmorInventory()->canAddItem($leggs)) {
					$player->getArmorInventory()->setLeggings($leggs);
				} else {
					$player->getLevel()->dropItem($player, $leggs);
				}
				if ($player->getArmorInventory()->canAddItem($boots)) {
					$player->getArmorInventory()->setBoots($boots);
				} else {
					$player->getLevel()->dropItem($player, $boots);
				}
				break;
		}

	}
}