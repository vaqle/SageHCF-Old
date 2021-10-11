<?php
namespace vale\hcf\sage\partneritems;


use muqsit\invmenu\tasks\DelayedFakeBlockDataNotifyTask;
use pocketmine\item\Item;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Server;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class PItemManager{

	//Partner Items
	public const BONE = 0;
	public const COMBO_ABLILITY = 1;
	public const LASSO = 2;
	public const SHOCK = 3;
	public const ANTI_TRAP_BEACON = 4;
	public const NINJA_STAR = 5;
	public const TIME_WARP = 6;
	public const PORTABLE_BARD = 7;
	public const LOGGER_BAIT = 8;
	public const DECOY = 9;
	public const STICK = 10;
	public const STRENGTH = 11;
	public const MEDKIT = 12;
	public const FOCUSMODE = 13;
	public const EFFECT_DISABLER = 14;
	public const BESERK_ABILITY = 15;
	public const HOE_OF_NOTCH = 16;

	//LootBoxes
	const SAGE = "Sage";
	const SUMMER = "Summer";
	const ABILITY = "ABILITY";

	//KEYALL TYPES


	public static function giveKeyAllVoucher(SagePlayer $player, string $keyVoucherId, int $amount = 1){
      switch ($keyVoucherId){
		  case self::SAGE:
			  $sage = Item::get(401, 0, $amount);
			  $sage->setCustomName("§r§l§5Sage Crate Key ALL §r§7(Right-Click)");
			  $sage->setLore([
				  "§r§7Right-Click to redeen this Key ALL voucher",
				  "§r§7and give the whole server a §eFREE KEY!",
				  "§r§7",
				  "§r§l§cWARNING: §r§7everyone will love your",
				  "§r§7after you use this Key ALL voucher!"
			  ]);
			  Sage::addItem($player, $sage);
		  	break;
		  case self::ABILITY:
			  $ability = Item::get(401, 0, $amount);
			  $ability->setCustomName("§r§l§cAbility Crate Key ALL §r§7(Right-Click)");
			  $ability->setLore([
				  "§r§7Right-Click to redeen this Key ALL voucher",
				  "§r§7and give the whole server a §eFREE KEY!",
				  "§r§7",
				  "§r§l§cWARNING: §r§7everyone will love your",
				  "§r§7after you use this Key ALL voucher!"
			  ]);
			  Sage::addItem($player, $ability);
		  	break;
	  }
	}

	public static function giveLootBox(SagePlayer $player, string $lootBoxId, int $amount = 1)
	{
		switch ($lootBoxId){
			case self::SAGE:
				$sage = Item::get(Item::CHEST, 1, $amount);
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
				$player->getInventory()->addItem($sage);
				break;
			case self::SUMMER:
				$summer = Item::get(Item::CHEST, 1, $amount);
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
				$player->getInventory()->addItem($summer);
				break;
		}
	}

	public static function giveLootboxItems(SagePlayer $player){
		$rewards = rand(1,5);
		$broadCast = Server::getInstance();
		switch ($rewards){
			case 1:
				$bone  = Item::get(Item::BONE, 0, rand(1,2))->
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
				#$broadCast->broadcastMessage("§r§6{$player->getName()} §r§ehas won §r§6{$rewards} §c§lBone's §r§7from a §r§eLootbox.");
				break;

			case 2:
				$anti = Item::get(Item::MELON_BLOCK, 0 , rand(1,2))->
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
				#$broadCast->broadcastMessage("§r§6{$player->getName()} §r§ehas won §r§6{$rewards} §b§lAnti Trap Beacon's §r§7from a §r§eLootbox.");
				break;

			case 3:
				$bard = Item::get(Item::DYE, 14 , rand(1,2))->
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
				#$broadCast->broadcastMessage("§r§6{$player->getName()} §r§ehas won §r§6{$rewards} §e§lPortable Bard's §r§7from a §r§eLootbox.");
				break;

			case 4:
				$combo = Item::get(Item::PUFFERFISH, 0, rand(1,2))->setCustomName("§r§b§lCombo Ability")->
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
				#$broadCast->broadcastMessage("§r§6{$player->getName()} §r§ehas won §r§6{$rewards} §b§lCombo Ability's §r§7from a §r§eLootbox.");
				break;

			case 5:
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
				#$broadCast->broadcastMessage("§r§6{$player->getName()} §r§ehas won §r§6{$rewards} §b§lNinja Star's §r§7from a §r§eLootbox.");
				break;
		}
	}

	public static function givePartnerItem(SagePlayer $player, int $partnerItemId, int $amount = 1){
		switch ($partnerItemId){
			case self::BONE;
				$bone  = Item::get(Item::BONE, 0, $amount)->
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
				$player->sendMessage("§r§eYou have recieved a {$bone->getCustomName()} §r§efrom a §r§6§lPartner Package");
				break;

			case self::MEDKIT:
				$medkit  = Item::get(Item::DYE, 1, $amount)->
				setCustomName("§r§c§lCombo's Medkit")->
				setLore([
					'§r§7Use this item to clutch up',
					'§r§7You will get absorption and Regen',
					'',
					'§r§7§oOnly enabled 1k blocks past overworld',
					'',
					'§r§eAvailable at §r§6store.hcf.net'
				]);
				$medkit->getNamedTag()->setTag(new StringTag("PartnerItem_MedKit"));
				$medkit->setNamedTagEntry(new ListTag("ench"));
				$player->getInventory()->addItem($medkit);
				$player->sendMessage("§r§eYou have recieved a {$medkit->getCustomName()} §r§efrom a §r§6§lPartner Package");
				break;
			case self::COMBO_ABLILITY;
				$combo = Item::get(Item::PUFFERFISH, 0 , $amount)->setCustomName("§r§b§lCombo Ability")->
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
				$player->sendMessage("§r§eYou have recieved a {$combo->getCustomName()} §r§efrom a §r§6§lPartner Package");
				break;
			case self::LASSO:
				$lasso  = Item::get(Item::LEAD, 0, $amount)->
				setCustomName("§r§b§lBirdo's Lasso")->
				setLore([
					'§r§7Hit a player three times consecutively',
					'§r§7to pull them towards you',
					'',
					'§r§7§oOnly enabled 1k blocks past overworld',
					'',
					'§r§eAvailable at §r§6store.hcf.net'
				]);
				$lasso->getNamedTag()->setTag(new StringTag("PartnerItem_Lasso"));
				$lasso->setNamedTagEntry(new ListTag("ench"));
				$player->getInventory()->addItem($lasso);
				$player->sendMessage("§r§eYou have recieved a {$lasso->getCustomName()} §r§efrom a §r§6§lPartner Package");
				break;

			case self::STRENGTH:
				$strength  = Item::get(Item::BLAZE_POWDER, 0, $amount)->
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
				$player->sendMessage("§r§eYou have recieved a {$strength->getCustomName()} §r§efrom a §r§6§lPartner Package");
				break;

			case self::SHOCK;
				$shock = Item::get(Item::GOLD_HOE, 0 , $amount)->
				setCustomName("§r§6§lCane's Shock Ability")->
				setLore([
					'§r§7Hit a player three times consecutively',
					'§r§7to spawn lightning on them and make them',
					'§r§7unaware of there surrondings',
					'',
					'§r§7§oOnly enabled 1k blocks past overworld',
					'',
					'§r§eAvailable at §r§6store.hcf.net'
				]);
				$shock->getNamedTag()->setTag(new StringTag("PartnerItem_Shock"));
				$shock->setNamedTagEntry(new ListTag("ench"));
				$player->getInventory()->addItem($shock);
				$player->sendMessage("§r§eYou have recieved a {$shock->getCustomName()} §r§efrom a §r§6§lPartner Package");

				break;
			case self::ANTI_TRAP_BEACON;
				$anti = Item::get(Item::MELON_BLOCK, 0 , $amount)->
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
				$player->sendMessage("§r§eYou have recieved a {$anti->getCustomName()} §r§efrom a §r§6§lPartner Package");
				break;
			case self::PORTABLE_BARD:
				$bard = Item::get(Item::DYE, 14 , $amount)->
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
				$player->sendMessage("§r§eYou have recieved a {$bard->getCustomName()} §r§efrom a §r§6§lPartner Package");
				break;
			case self::DECOY:
				$decoy = Item::get(351, 7 , $amount)->
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
				$player->sendMessage("§r§eYou have recieved a {$decoy->getCustomName()} §r§efrom a §r§6§lPartner Package");
				break;
			case self::STICK;
				$stick = Item::get(Item::STICK, 0 , $amount)->
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
				$player->sendMessage("§r§eYou have recieved a {$stick->getCustomName()} §r§efrom a §r§6§lPartner Package");
				break;

			case self::NINJA_STAR:
				$ninja = Item::get(Item::NETHER_STAR, 0 , $amount)->
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
				$player->sendMessage("§r§eYou have recieved a {$ninja->getCustomName()} §r§efrom a §r§6§lPartner Package");
				break;


			case self::FOCUSMODE:
				$focusmode = Item::get(Item::GOLD_NUGGET, 0 , $amount)->
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
				$player->getInventory()->addItem($focusmode);
				$player->sendMessage("§r§eYou have recieved a {$focusmode->getCustomName()} §r§efrom a §r§6§lPartner Package");
				break;


			case self::LOGGER_BAIT:
				$logger = Item::get(Item::EGG, 0 , $amount)->
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
				$player->sendMessage("§r§eYou have recieved a {$logger->getCustomName()} §r§efrom a §r§6§lPartner Package");
				break;


		}
	}

	public static function givePartnerPackage(SagePlayer $player, int $amount){
		$id = mt_rand(1,1000);
		$comma = "§r§8,";
		$partnerpackage = Item::get(Item::ENDER_CHEST, 0, $amount)->
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
		$player->getInventory()->addItem($partnerpackage);
	}


	public static function giveKeys(SagePlayer $player, string $type, $amount){
		switch ($type){
			case "Haze":
				$key = Item::get(Item::DYE, 9, $amount);
				$key->setCustomName("§r§d§lHaze §r§7Key (Right-Click)");
				$key->setLore([
					"§r§7Right-Click this key on the §l§dHaze",
					"§r§l§dCrate §r§7located at §dspawn §7to obtain rewards.",
					"§r",
					"§r§dstore.buycraft.net"
				]);
				$key->setNamedTagEntry(new ListTag("ench"));
				$key->getNamedTag()->setTag(new StringTag("hazekeyxd"));
				$player->getInventory()->addItem($key);
				break;

			case "Aegis":
				$key = Item::get(Item::TRIPWIRE_HOOK, 0, $amount);
				$key->setCustomName("§r§e§lAegis §r§7Key (Right-Click)");
				$key->setLore([
					"§r§7Right-Click this key on the §l§eAegis",
					"§r§l§eCrate §r§7located at §espawn §7to obtain rewards.",
					"§r",
					"§r§estore.buycraft.net"
				]);
				$key->setNamedTagEntry(new ListTag("ench"));
				$key->getNamedTag()->setTag(new StringTag("aegiskeyxd"));
				$player->getInventory()->addItem($key);
				break;

			case "Sage":
				$key = Item::get(Item::TRIPWIRE_HOOK, 0, $amount);
				$key->setCustomName("§r§5§lSage §r§7Key (Right-Click)");
				$key->setLore([
					"§r§7Right-Click this key on the §l§5Spawn",
					"§r§l§5Crate §r§7located at §5spawn §7to obtain rewards.",
					"§r",
					"§r§5store.buycraft.net"
				]);
				$key->setNamedTagEntry(new ListTag("ench"));
				$key->getNamedTag()->setTag(new StringTag("sagekeyxd"));
				$player->getInventory()->addItem($key);
				break;

			case "Summer":
				$summerob = Item::get(Item::ENDER_EYE, 2 , $amount)->
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
				$player->getInventory()->addItem($summerob);
				break;
			case "Ability":
				$key = Item::get(Item::TRIPWIRE_HOOK, 0, $amount);
				$key->setCustomName("§r§c§lAbility §r§7Key (Right-Click)");
				$key->setLore([
					"§r§7Right-Click this key on the §l§cAbility",
					"§r§l§cCrate §r§7located at §cspawn §7to obtain rewards.",
					"§r",
					"§r§cstore.buycraft.net"
				]);
				$key->setNamedTagEntry(new ListTag("ench"));
				$key->getNamedTag()->setTag(new StringTag("abilitykeyxd"));
				$player->getInventory()->addItem($key);
				break;
		}
	}

	public static function giveAirDrops(SagePlayer $player, int $amount){
		$lol = Item::get(Item::DISPENSER, 0 ,$amount);
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

	}

	public static function itemRand(string $items)
	{
		switch ($items) {
			case "rewards":
				$amount = mt_rand(1, 3);

				$lasso  = Item::get(Item::LEAD, 0, $amount)->
				setCustomName("§r§b§lBirdo's Lasso")->
				setLore([
					'§r§7Hit a player three times consecutively',
					'§r§7to pull them towards you',
					'',
					'§r§7§oOnly enabled 1k blocks past overworld',
					'',
					'§r§eAvailable at §r§6store.hcf.net'
				]);
				$lasso->getNamedTag()->setTag(new StringTag("PartnerItem_Lasso"));
				$lasso->setNamedTagEntry(new ListTag("ench"));

				$medkit  = Item::get(Item::DYE, 1, $amount)->
				setCustomName("§r§c§lCombo's Medkit")->
				setLore([
					'§r§7Use this item to clutch up',
					'§r§7You will get absorption and Regen',
					'',
					'§r§7§oOnly enabled 1k blocks past overworld',
					'',
					'§r§eAvailable at §r§6store.hcf.net'
				]);
				$medkit->getNamedTag()->setTag(new StringTag("PartnerItem_MedKit"));
				$medkit->setNamedTagEntry(new ListTag("ench"));

				$logger = Item::get(Item::EGG, 0 , $amount)->
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
				$stick = Item::get(Item::STICK, 0 , $amount)->
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
				$decoy = Item::get(351, 7, $amount)->
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

				$bone = Item::get(Item::BONE, 0, rand(1, 2))->
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

				$anti = Item::get(Item::MELON_BLOCK, 0, rand(1, 2))->
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
				$bard = Item::get(Item::DYE, 14, rand(1, 2))->
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
				$combo = Item::get(Item::PUFFERFISH, 0, rand(1, 2))->setCustomName("§r§b§lCombo Ability")->
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

				$ninja = Item::get(Item::NETHER_STAR, 0, rand(1, 2))->
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
				
					$strength  = Item::get(Item::BLAZE_POWDER, 0, $amount)->
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

				$focusmode = Item::get(Item::GOLD_NUGGET, 0 , $amount)->
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
		
				break;
		}
		$rewards = [$anti,$bard, $ninja, $combo, $bone, $decoy, $logger, $stick, $strength, $medkit, $lasso, $focusmode];
		$reward = $rewards[array_rand($rewards)];

		return $reward;
	}

}