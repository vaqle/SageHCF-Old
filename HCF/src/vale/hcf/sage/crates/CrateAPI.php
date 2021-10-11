<?php

declare(strict_types = 1);


namespace vale\hcf\sage\crates;

//Base Libraries
use pocketmine\{math\Vector3, Server, Player};
use pocketmine\item\{Item, enchantment\Enchantment, enchantment\EnchantmentInstance};
use pocketmine\utils\Config;
use pocketmine\block\Block;
use pocketmine\nbt\tag\{CompoundTag, IntTag, StringTag, ListTag};
use vale\hcf\sage\kits\KitManager;
use vale\hcf\sage\partneritems\PItemManager;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\system\enchant\EnchantUtils;
use vale\hcf\sage\tasks\sql\PingTask;

class CrateAPI
{
	public static $api;

	public static function hazeRewards(SagePlayer $player)
	{
		self::$api = new CrateRewards();
		$inv = $player->getInventory();
		$chances = mt_rand(1, 7);
		switch ($chances) {
			case 1:
				$levels = mt_rand(1, 2);
				$helmet = Item::get(Item::DIAMOND_HELMET);
				$chestplate = Item::get(Item::DIAMOND_CHESTPLATE);
				$pick = Item::get(Item::DIAMOND_PICKAXE);
				$helmet->setCustomName("§r§d§lHaze §r§7Helmet");
				$chestplate->setCustomName("§r§d§lHaze §r§7Chestplate");
				$pick->setCustomName("§r§d§lHaze §r§7Pick");
				$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), $levels));
				$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), $levels));
				$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), $levels));
				$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), $levels));
				$pick->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY), $levels));

				if ($inv->canAddItem($helmet) && $chestplate && $pick) {
					$player->getInventory()->addItem($helmet);
					$player->getInventory()->addItem($chestplate);
					$player->getInventory()->addItem($pick);
				} else {
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $helmet);
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $chestplate);
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $pick);
					$player->sendMessage("§r§cYour inventory is full.");
				}
				break;
			case 2:
				$gold = Item::get(Item::GOLD_BLOCK, 0, mt_rand(1, 32));
				$diamond = Item::get(Item::DIAMOND_BLOCK, 0, mt_rand(1, 32));
				$iron = Item::get(Item::IRON_BLOCK, 0, mt_rand(1, 32));
				if ($inv->canAddItem($gold)) {
					$player->getInventory()->addItem($gold);
					$player->getInventory()->addItem($diamond);
					$player->getInventory()->addItem($gold);
				} else {
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $gold);
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $diamond);
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $iron);
					$player->sendMessage("§r§cYour inventory is full.");
				}

				break;
			case 3:
				$goldapple = Item::get(Item::GOLDEN_APPLE, 0, mt_rand(1, 5));
				if ($inv->canAddItem($goldapple)) {
					$player->getInventory()->addItem($goldapple);
				} else {
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $goldapple);
					$player->sendMessage("§r§cYour inventory is full.");
				}

				break;
			case 4:
				$goldenapple = Item::get(Item::GOLDEN_APPLE, 0, 4);
				break;
			case 5:
				$gold = Item::get(Item::GOLD_BLOCK, 0, mt_rand(1, 32));
				$diamond = Item::get(Item::DIAMOND_BLOCK, 0, mt_rand(1, 32));
				$iron = Item::get(Item::IRON_BLOCK, 0, mt_rand(1, 32));
				if ($inv->canAddItem($gold)) {
					$player->getInventory()->addItem($gold);
					$player->getInventory()->addItem($diamond);
					$player->getInventory()->addItem($gold);
				} else {
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $gold);
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $diamond);
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $iron);
					$player->sendMessage("§r§cYour inventory is full.");
				}
				break;
			case 6:
				$levels = mt_rand(1, 2);
				$leggings = Item::get(Item::DIAMOND_LEGGINGS);
				$boots = Item::get(Item::DIAMOND_BOOTS);
				$boots->setCustomName("§r§d§lHaze §r§7Boots");
				$leggings->setCustomName("§r§d§lHaze §r§7leggings");
				$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), $levels));
				$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), $levels));
				$leggings->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), $levels));
				$leggings->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), $levels));

				if ($inv->canAddItem($boots) && $leggings) {
					$player->getInventory()->addItem($leggings);
					$player->getInventory()->addItem($boots);
				} else {
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $boots);
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $leggings);
					$player->sendMessage("§r§cYour inventory is full.");
				}
				break;

			case 7:
				$ep = Item::get(Item::ENDER_PEARL, 0, 16);
				$emerald = Item::get(Item::EMERALD_BLOCK, 0, rand(1,42));
				$player->getInventory()->addItem($ep);
				$player->getInventory()->addItem($emerald);
				break;
		}
	}

	public static function giveAbilityRewards(SagePlayer $player){
		self::$api = new CrateRewards();
		$inv = $player->getInventory();
		$chances = mt_rand(1, 7);
		switch ($chances){
			case 1:
				$bone  = Item::get(Item::BONE, 0, rand(1,4))->
				setCustomName("§r§c§lVales's Bone")->
				setLore([
					'§r§7Hit a player three times consecutively',
					'§r§7to prevent them from building for a total of 15 seconds',
					'',
					'§r§7§oOnly enabled 1k blocks past overworld',
					'',
					'§r§eAvailable at §r§6store.hcf.net'
				]);
				$bone->getNamedTag()->setTag(new StringTag("PartnerItem_Bone"));
				$bone->setNamedTagEntry(new ListTag("ench"));
				$player->getInventory()->addItem($bone);
				break;

			case 2:
				$anti = Item::get(Item::MELON_BLOCK, 0 , rand(1,3))->
				setCustomName("§r§5§lMarios's Anti Trap Beacon")->
				setLore([
					'§r§7Place this to prevent a player from placing blocks',
					'§r§7until this block is broken (10 block radius).',
					'',
					'§r§7§oOnly enabled 1k blocks past overworld',
					'',
					'§r§eAvailable at §r§6store.hcf.net'
				]);
				$anti->getNamedTag()->setTag(new StringTag("PartnerItem_BEACON"));
				$anti->setNamedTagEntry(new ListTag("ench"));
				$player->getInventory()->addItem($anti);
				break;

			case 3:
				$bard = Item::get(Item::DYE, 14 , rand(1,4))->
				setCustomName("§r§e§lBambes's Portable Bard")->
				setLore([
					'§r§7Place \ throw this to spawn in your own personal bard',
					'§r§7This can act like a regular player and give youe effects.',
					'§r§7This entity can take damage so use it wisely.',
					'',
					'§r§7§oOnly enabled 1k blocks past overworld',
					'',
					'§r§eAvailable at §r§6store.hcf.net'
				]);
				$bard->getNamedTag()->setTag(new StringTag("PartnerItem_BARD"));
				$bard->setNamedTagEntry(new ListTag("ench"));
				$player->getInventory()->addItem($bard);
				break;

			case 4:
				$decoy = Item::get(351, 7 , rand(1,3))->
				setCustomName("§r§3§lFear's Decoy Device")->
				setLore([
					'§r§7Place this to summon an army of servants.',
					'§r§7This will spawn multiple decoys to confuse and annoy the enemy.',
					'§r§7This entity can take damage so use it wisely.',
					'',
					'§r§7§oOnly enabled 1k blocks past overworld',
					'',
					'§r§eAvailable at §r§6store.hcf.net'
				]);
				$decoy->getNamedTag()->setTag(new StringTag("PartnerItem_DECOY"));
				$decoy->setNamedTagEntry(new ListTag("ench"));
				$player->getInventory()->addItem($decoy);
				break;

			case 5:
				$lol = Item::get(Item::DISPENSER, 0 ,rand(1,2));
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
				$player->getInventory()->addItem($lol);
				break;

			case 6:
				$ninja = Item::get(Item::NETHER_STAR, 0 , rand(1,2))->
				setCustomName("§r§5§lNinja Star")->
				setLore([
					'§r§7Click this to start a Queue To Teleport to Your Last Damager.',
					'§r§7This will aler them however with a distinct sound.',
					'§r§7Use this item wisely.',
					'',
					'§r§7§oOnly enabled 1k blocks past overworld',
					'',
					'§r§eAvailable at §r§6store.hcf.net'
				]);
				$ninja->getNamedTag()->setTag(new StringTag("PartnerItem_NINJA"));
				$ninja->setNamedTagEntry(new ListTag("ench"));
				$player->getInventory()->addItem($ninja);
				break;

			case 7:
				$combo = Item::get(Item::PUFFERFISH, 0 , rand(1,2))->setCustomName("§r§b§lCombo Ability")->
				setLore([
					'§r§7Hit a player three times consecutively',
					'§r§7to make them take combo ladder kb for 6 seconds',
					'',
					'§r§7§oOnly enabled 1k blocks past overworld',
					'',
					'§r§eAvailable at §r§6store.hcf.net'
				]);
				$combo->getNamedTag()->setTag(new StringTag("PartnerItem_Combo"));
				$combo->setNamedTagEntry(new ListTag("ench"));
				$player->getInventory()->addItem($combo);
				break;

		}

	}


	public static function giveSageRewards(SagePlayer $player){
		$rewards = rand(1,10);
		$inv = $player->getInventory();
		switch ($rewards){
			case 1:
				$helmet = Item::get(Item::DIAMOND_HELMET, 0 , 1);
				$helmet->setCustomName("§r§5§lSage §r§7Helmet");
				#$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(KitManager::NV),1));
				$helmet->setLore([
					'§r§4Night Vision II'
				]);
				EnchantUtils::addEnchanmentToItem($helmet, ["Protection", "Unbreaking"], 2);
				$chestplate = Item::get(Item::DIAMOND_CHESTPLATE, 0, 1);
				$chestplate->setCustomName("§r§5§lSage §r§7Chestplate");
				$chestplate->setLore([
					'§r§4Magma I',
				]);
				EnchantUtils::addEnchanmentToItem($chestplate, ["Protection", "Unbreaking"],2);
				#$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(KitManager::MAGMA),1));
				if ($inv->canAddItem($helmet) && $chestplate) {
					$player->getInventory()->addItem($helmet);
					$player->getInventory()->addItem($chestplate);
				} else {
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $helmet);
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $chestplate);
					$player->sendMessage("§r§cYour inventory is full.");
				}
				break;
			case 2:
				$leggings = Item::get(Item::DIAMOND_LEGGINGS, 0 , 1);
				$leggings->setCustomName("§r§5§lSage §r§7Helmet");
				#$leggings->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(KitManager::NV),1));
				EnchantUtils::addEnchanmentToItem($leggings, ["Protection", "Unbreaking"], 2);
				$boots = Item::get(Item::DIAMOND_BOOTS, 0, 1);
				$boots->setCustomName("§r§5§lSage §r§7Boots");
				$boots->setLore([
					'§r§4Speed II',
				]);
				EnchantUtils::addEnchanmentToItem($boots, ["Protection", "Unbreaking"],2);
				#$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(KitManager::SPEED),2));

				#$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(KitManager::MAGMA),1));
				if ($inv->canAddItem($boots) && $leggings) {
					$player->getInventory()->addItem($leggings);
					$player->getInventory()->addItem($boots);
				} else {
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $leggings);
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $boots);
					$player->sendMessage("§r§cYour inventory is full.");
				}
				break;
			case 3:
				$sword = Item::get(Item::DIAMOND_SWORD);
				$sword->setCustomName("§r§5§lSage §r§7Sword");
				$sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS),2));
				$sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING),2));

				if($inv->canAddItem($sword)){
					$inv->addItem($sword);
				}else{
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $sword);
					$player->sendMessage("§r§cYour inventory is full.");
				}
				break;

			case 4:
				$gold = Item::get(Item::GOLD_BLOCK,0,32);
				$iron = Item::get(Item::IRON_BLOCK, 0 ,32);
				$emerald = Item::get(Item::EMERALD_BLOCK,0,32);
				$diamond = Item::get(Item::DIAMOND_BLOCK,0,32);

				if($inv->canAddItem($gold) && $inv->canAddItem($iron) && $inv->canAddItem($emerald) && $inv->canAddItem($diamond)){
					$inv->addItem($gold);
					$inv->addItem($diamond);
					$inv->addItem($emerald);
					$inv->addItem($iron);
				}else{
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $emerald);
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $diamond);
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $gold);
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $iron);
					$player->sendMessage("§r§cYour inventory is full.");

				}
				break;
			case 5:
				$gapple = Item::get(Item::GOLDEN_APPLE);
				PItemManager::givePartnerPackage($player,1);
				$pearls = Item::get(Item::ENDER_PEARL, 0 ,16);

				if($inv->canAddItem($gapple) && $pearls){
					$inv->addItem($gapple);
					$inv->addItem($pearls);
				}else{
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $gapple);
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $pearls);
					$player->sendMessage("§r§cYour inventory is full.");
				}
				break;

			case 6:
				$sage = Item::get(Item::CHEST, 1, rand(1,2));
				$sage->setCustomName("§r§l§gSage Lootbox §r§7(Right-Click) §f(#0054)");
				$sage->setNamedTagEntry(new ListTag("ench"));
				$sage->getNamedTag()->setTag(new StringTag("lootbox_sage"));
				$sage->setLore([
					"§r§7A exclusive item contained with great items",
					"§r§7this item is sacred and can be obtained on our store",
					"§r§7rewards (8-10)",
					"§r§6 * Sage Map 1 *",
					"",
					"§r§l§cNOTE: §r§7place to get compiled items"
				]);
				Sage::addItem($player,$sage);
				break;

			case 7:
				$summer = Item::get(Item::CHEST, 1, rand(1,2));
				$summer->setCustomName("§r§l§dSummer Lootbox §r§7(Right-Click) §f(#0054)");
				$summer->setNamedTagEntry(new ListTag("ench"));
				$summer->getNamedTag()->setTag(new StringTag("lootbox_summer"));
				$summer->setLore([
					"§r§7A exclusive item contained with great items",
					"§r§7this item is sacred and can be obtained on our store",
					"§r§7rewards (8-10)",
					"§r§6 * Sage Map 1 *",
					"",
					"§r§l§cNOTE: §r§7place to get compiled items"
				]);
				Sage::addItem($player,$summer);
				break;

			case 8:
				$id = mt_rand(1,1000);
				$comma = "§r§8,";
				$partnerpackage = Item::get(Item::ENDER_CHEST, 0, rand(1,2))->
				setCustomName("§r§5§lPartner Package §r§f#{$id}")
					->setLore([
						'§r§7Right click or place to open and redeem rewards.',
						'§r§c§lPossible Rewards',
						'',
						'§r§7§l* §6§lVales Bone §r§8(§r§7bone§r§8,§r§7antibuild§r§8,§r§7antitrapper§r§8)',
						'§r§7§l* §a§lStarvation Flesh §r§8(§r§7starveflesh§r§8,§r§7starve§r§8)',
						'§r§7§l* §c§lPotion Counter §r§8(§r§7potcounter§r§8)',
						'§r§7§l* §r§3§lFears Decoy Device §r§8(§r§7decoy§r§8,§r§7device§r§8)',
						'',
						'§r§eAvailable at §r§6store.hcf.net'
					]);
				$partnerpackage->getNamedTag()->setTag(new StringTag("PartnerPackage"));
				$partnerpackage->setNamedTagEntry(new ListTag("ench"));
				Sage::addItem($player,$partnerpackage);
				break;

			case 9:
				$focusmode = Item::get(Item::GOLD_NUGGET, 0 , rand(1,4))->
				setCustomName("§r§e§lAnt's Focus Mode")->
				setLore([
					'§r§7Hit a player to focus them this will deal more damage.',
					'§r§7Upon focusing you deal 2x more damage.',
					'§r§7Use this item wisely.',
					'',
					'§r§7§oOnly enabled 1k blocks past overworld',
					'',
					'§r§eAvailable at §r§6store.hcf.net'
				]);
				$focusmode->getNamedTag()->setTag(new StringTag("PartnerItem_Focus"));
				$focusmode->setNamedTagEntry(new ListTag("ench"));
				Sage::addItem($player,$focusmode);
				break;

			case 10:
			case "Summer":
				$summerob = Item::get(Item::ENDER_EYE, 2 , rand(1,2))->
				setCustomName("§r§5§kkeeueueu§r§d§lSummer Orb 2.0§r§5§kkeeueueu")->
				setLore([
					'§r§7Tap this §r§d§lsacred §r§7item at the Orb Extractor',
					'§r§7To recieve rewards such as',
					'§r§d(§r§7Airdrops§r§d, §r§7Keys§r§d, §r§7Portable Kits§r§d',
					'§r§7Ranks§r§d, §r§7and More!§r§d)',
					'',
					'§r§d§lstore.hcf.net'
				]);
				$summerob->getNamedTag()->setTag(new StringTag("summerkeyxd"));
				Sage::addItem($player,$summerob);
				break;
		}
	}


	public static function giveSummerRewards(SagePlayer $player){
		self::$api = new CrateRewards();
		$inv = $player->getInventory();
		$chances = mt_rand(1, 9);
		switch ($chances){
			case 1:
				PItemManager::givePartnerPackage($player,15);
				Server::getInstance()->broadcastMessage("§r§d{$player->getName()} §r§7has opened a §r§5§kejje  §r§d§lSUMMER 2.0 CRATE §r§5§kejje§r§d §r§7and recieved §r§d§l15 §r§5§lPartnerPackages.");
				break;
			case 2:
				PItemManager::giveAirDrops($player,6);
				Server::getInstance()->broadcastMessage("§r§d{$player->getName()} §r§7has opened a §r§5§kejje  §r§d§lSUMMER 2.0 CRATE §r§5§kejje§r§d §r§7and recieved §r§d§l6 §r§b§lAirdrops.");
				break;

			case 3:
				$bone  = Item::get(Item::BONE, 0, 32)->
				setCustomName("§r§c§lVales's Bone")->
				setLore([
					'§r§7Hit a player three times consecutively',
					'§r§7to prevent them from building for a total of 15 seconds',
					'',
					'§r§7§oOnly enabled 1k blocks past overworld',
					'',
					'§r§eAvailable at §r§6store.hcf.net'
				]);
				$bone->getNamedTag()->setTag(new StringTag("PartnerItem_Bone"));
				$bone->setNamedTagEntry(new ListTag("ench"));
				$player->getInventory()->addItem($bone);
				Server::getInstance()->broadcastMessage("§r§d{$player->getName()} §r§7has opened a §r§5§kejje  §r§d§lSUMMER 2.0 CRATE §r§5§kejje§r§d §r§7and recieved §r§d§l32x §r§6§lVale's Bones.");
				break;

			case 4:
				$combo = Item::get(Item::PUFFERFISH, 0 , rand(1,10))->setCustomName("§r§b§lCombo Ability")->
				setLore([
					'§r§7Hit a player three times consecutively',
					'§r§7to make them take combo ladder kb for 6 seconds',
					'',
					'§r§7§oOnly enabled 1k blocks past overworld',
					'',
					'§r§eAvailable at §r§6store.hcf.net'
				]);
				$combo->getNamedTag()->setTag(new StringTag("PartnerItem_Combo"));
				$combo->setNamedTagEntry(new ListTag("ench"));
				$player->getInventory()->addItem($combo);
				Server::getInstance()->broadcastMessage("§r§d{$player->getName()} §r§7has opened a §r§5§kejje  §r§d§lSUMMER 2.0 CRATE §r§5§kejje§r§d §r§7and recieved §r§d§lx(1-7) §r§b§lCombo Abilitys.");
				break;

			case 5:
				$ninja = Item::get(Item::NETHER_STAR, 0 , rand(1,10))->
				setCustomName("§r§5§lNinja Star")->
				setLore([
					'§r§7Click this to start a Queue To Teleport to Your Last Damager.',
					'§r§7This will aler them however with a distinct sound.',
					'§r§7Use this item wisely.',
					'',
					'§r§7§oOnly enabled 1k blocks past overworld',
					'',
					'§r§eAvailable at §r§6store.hcf.net'
				]);
				$ninja->getNamedTag()->setTag(new StringTag("PartnerItem_NINJA"));
				$ninja->setNamedTagEntry(new ListTag("ench"));
				$player->getInventory()->addItem($ninja);
				Server::getInstance()->broadcastMessage("§r§d{$player->getName()} §r§7has opened a §r§5§kejje  §r§d§lSUMMER 2.0 CRATE §r§5§kejje§r§d §r§7and recieved §r§d§lx(1-7) §r§5§lNinja Stars.");
				break;

			case 6:
				$anti = Item::get(Item::MELON_BLOCK, 0 , rand(1,10))->
				setCustomName("§r§5§lMarios's Anti Trap Beacon")->
				setLore([
					'§r§7Place this to prevent a player from placing blocks',
					'§r§7until this block is broken (10 block radius).',
					'',
					'§r§7§oOnly enabled 1k blocks past overworld',
					'',
					'§r§eAvailable at §r§6store.hcf.net'
				]);
				$anti->getNamedTag()->setTag(new StringTag("PartnerItem_BEACON"));
				$anti->setNamedTagEntry(new ListTag("ench"));
				$player->getInventory()->addItem($anti);
				Server::getInstance()->broadcastMessage("§r§d{$player->getName()} §r§7has opened a §r§5§kejje  §r§d§lSUMMER 2.0 CRATE §r§5§kejje§r§d §r§7and recieved §r§d§lx(1-7) §r§a§lAnti Trap Beacons.");
				break;

			case 7:
				$strength  = Item::get(Item::BLAZE_POWDER, 0, rand(1,2))->
				setCustomName("§r§c§lStrength II")->
				setLore([
					'§r§7Click this item to recieve the following effects',
					'§r§7Strength II (5-7) Seconds',
					'',
					'§r§7§oOnly enabled 1k blocks past overworld',
					'',
					'§r§eAvailable at §r§6store.hcf.net'
				]);
				$strength->getNamedTag()->setTag(new StringTag("PartnerItem_Strength"));
				$strength->setNamedTagEntry(new ListTag("ench"));
				$player->getInventory()->addItem($strength);
				break;

			case 8:
				$stick = Item::get(Item::STICK, 0 ,  rand(1,2))->
				setCustomName("§r§4§lStick of Confusion")->
				setLore([
					'§r§7Hit your enemies to make them confused.',
					'§r§7This will make them look up!',
					'§r§7Use this item wisely.',
					'',
					'§r§7§oOnly enabled 1k blocks past overworld',
					'',
					'§r§eAvailable at §r§6store.hcf.net'
				]);
				$stick->getNamedTag()->setTag(new StringTag("PartnerItem_Stick"));
				$stick->setNamedTagEntry(new ListTag("ench"));
				$player->getInventory()->addItem($stick);
				break;

			case 9:
				$logger = Item::get(Item::EGG, 0 ,  rand(1,2))->
				setCustomName("§r§6§lLogger Bait")->
				setLore([
					'§r§7Click this to manipulate your enemies.',
					'§r§7This will spawn a fake logger..',
					'§r§7Use this item wisely.',
					'',
					'§r§7§oOnly enabled 1k blocks past overworld',
					'',
					'§r§eAvailable at §r§6store.hcf.net'
				]);
				$logger->getNamedTag()->setTag(new StringTag("PartnerItem_Logger"));
				$logger->setNamedTagEntry(new ListTag("ench"));
				$player->getInventory()->addItem($logger);
				break;


		}

	}


	public static function giveAntFocusModes(SagePlayer $player)
	{
		self::$api = new CrateRewards();
		$inv = $player->getInventory();
		$chances = mt_rand(1, 6);
		switch ($chances) {
			case 1:
				$helmet = Item::get(Item::DIAMOND_HELMET, 0, 1);
				$helmet->setCustomName("§r§e§lAnt's §r§7Helmet");
				#$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(KitManager::NV), 1));
				$helmet->setLore([
					'§r§4Night Vision II'
				]);
				EnchantUtils::addEnchanmentToItem($helmet, ["Protection", "Unbreaking"], 2);
				$chestplate = Item::get(Item::DIAMOND_CHESTPLATE, 0, 1);
				$chestplate->setCustomName("§r§e§lAnt's  §r§7Chestplate");
				$chestplate->setLore([
					'§r§4Magma I',
				]);
				EnchantUtils::addEnchanmentToItem($chestplate, ["Protection", "Unbreaking"], 2);
				#$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(KitManager::MAGMA), 1));
				if ($inv->canAddItem($helmet) && $chestplate) {
					$player->getInventory()->addItem($helmet);
					$player->getInventory()->addItem($chestplate);
				} else {
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $helmet);
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $chestplate);
					$player->sendMessage("§r§cYour inventory is full.");
				}
				break;
			case 2:
				$leggings = Item::get(Item::DIAMOND_LEGGINGS, 0, 1);
				$leggings->setCustomName("§r§e§lAnt's §r§7Leggings");
				#$leggings->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(KitManager::NV), 1));
				EnchantUtils::addEnchanmentToItem($leggings, ["Protection", "Unbreaking"], 2);
				$boots = Item::get(Item::DIAMOND_BOOTS, 0, 1);
				$boots->setCustomName("§r§e§lAnt's  §r§7Boots");
				$boots->setLore([
					'§r§4Speed II',
				]);
				EnchantUtils::addEnchanmentToItem($boots, ["Protection", "Unbreaking"], 2);
				#$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(KitManager::SPEED), 2));

				#$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(KitManager::MAGMA), 1));
				if ($inv->canAddItem($boots) && $leggings) {
					$player->getInventory()->addItem($leggings);
					$player->getInventory()->addItem($boots);
				} else {
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $leggings);
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $boots);
					$player->sendMessage("§r§cYour inventory is full.");
				}
				break;
			case 3:
				$sword = Item::get(Item::DIAMOND_SWORD);
				$sword->setCustomName("§r§e§lAnt's §r§7Boots");
				$sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 2));
				$sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
				if ($inv->canAddItem($sword)) {
					$inv->addItem($sword);
				} else {
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $sword);
					$player->sendMessage("§r§cYour inventory is full.");
				}
				break;

			case 4:
				PItemManager::givePartnerPackage($player, 2);
				#Server::getInstance()->broadcastMessage("§r§d{$player->getName()} §r§7has opened a §r§5§kejje  §r§d§lSUMMER 2.0 CRATE §r§5§kejje§r§d §r§7and recieved §r§d§l15 §r§5§lPartnerPackages.");
				break;
			case 5:
				PItemManager::giveAirDrops($player, 1);
				#Server::getInstance()->broadcastMessage("§r§d{$player->getName()} §r§7has opened a §r§5§kejje  §r§d§lSUMMER 2.0 CRATE §r§5§kejje§r§d §r§7and recieved §r§d§l6 §r§b§lAirdrops.");
				break;

			case 6:
				PItemManager::givePartnerItem($player, PItemManager::FOCUSMODE, rand(1, 6));
				break;
		}
	}



	public static function giveBirdoRewards(SagePlayer $player)
	{
		self::$api = new CrateRewards();
		$inv = $player->getInventory();
		$chances = mt_rand(1, 6);
		switch ($chances) {
			case 1:
				$helmet = Item::get(Item::DIAMOND_HELMET, 0, 1);
				$helmet->setCustomName("§r§b§lBirdos §r§7Helmet");
				#$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(KitManager::NV), 1));
				$helmet->setLore([
					'§r§4Night Vision II'
				]);
				EnchantUtils::addEnchanmentToItem($helmet, ["Protection", "Unbreaking"], 2);
				$chestplate = Item::get(Item::DIAMOND_CHESTPLATE, 0, 1);
				$chestplate->setCustomName("§r§b§lBirdos  §r§7Chestplate");
				$chestplate->setLore([
					'§r§4Magma I',
				]);
				EnchantUtils::addEnchanmentToItem($chestplate, ["Protection", "Unbreaking"], 2);
				#$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(KitManager::MAGMA), 1));
				if ($inv->canAddItem($helmet) && $chestplate) {
					$player->getInventory()->addItem($helmet);
					$player->getInventory()->addItem($chestplate);
				} else {
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $helmet);
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $chestplate);
					$player->sendMessage("§r§cYour inventory is full.");
				}
				break;
			case 2:
				$leggings = Item::get(Item::DIAMOND_LEGGINGS, 0, 1);
				$leggings->setCustomName("§r§b§lBirdos  §r§7Leggings");
				#$leggings->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(KitManager::NV), 1));
				EnchantUtils::addEnchanmentToItem($leggings, ["Protection", "Unbreaking"], 2);
				$boots = Item::get(Item::DIAMOND_BOOTS, 0, 1);
				$boots->setCustomName("§r§b§lBirdos  §r§7Boots");
				$boots->setLore([
					'§r§4Speed II',
				]);
				EnchantUtils::addEnchanmentToItem($boots, ["Protection", "Unbreaking"], 2);
				#$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(KitManager::SPEED), 2));

				#$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(KitManager::MAGMA), 1));
				if ($inv->canAddItem($boots) && $leggings) {
					$player->getInventory()->addItem($leggings);
					$player->getInventory()->addItem($boots);
				} else {
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $leggings);
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $boots);
					$player->sendMessage("§r§cYour inventory is full.");
				}
				break;
			case 3:
				$sword = Item::get(Item::DIAMOND_SWORD);
				$sword->setCustomName("§r§b§lBirdos  §r§7Boots");
				$sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 2));
				$sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));
				if ($inv->canAddItem($sword)) {
					$inv->addItem($sword);
				} else {
					$player->getLevel()->dropItem(new Vector3($player->getX(), $player->getY(), $player->getZ()), $sword);
					$player->sendMessage("§r§cYour inventory is full.");
				}
				break;

			case 4:
				PItemManager::givePartnerPackage($player, 2);
				#Server::getInstance()->broadcastMessage("§r§d{$player->getName()} §r§7has opened a §r§5§kejje  §r§d§lSUMMER 2.0 CRATE §r§5§kejje§r§d §r§7and recieved §r§d§l15 §r§5§lPartnerPackages.");
				break;
			case 5:
				PItemManager::giveAirDrops($player, 1);
				#Server::getInstance()->broadcastMessage("§r§d{$player->getName()} §r§7has opened a §r§5§kejje  §r§d§lSUMMER 2.0 CRATE §r§5§kejje§r§d §r§7and recieved §r§d§l6 §r§b§lAirdrops.");
				break;

			case 6:
				PItemManager::givePartnerItem($player, PItemManager::LASSO, rand(1, 6));
				break;
		}
	}

	/**
	 * @return CrateRewards
	 */
	public static function getCrateRewards(): CrateRewards{
		return self::$api ?? new CrateRewards();
	}
}