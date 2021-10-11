<?php
namespace vale\hcf\sage\models\util;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\MenuIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use vale\hcf\sage\kits\KitManager;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\system\enchant\EnchantUtils;

class CratePreviews{

	public static function sendHazePreview(SagePlayer $player, bool $message = true){
		$menu = InvMenu::create(MenuIds::TYPE_CHEST);
		$menu->setName("§r§7Haze_Crate");
		$menu->readonly(true);
		$ironBlocks = Item::get(Item::IRON_BLOCK,0,32);
		$summerob = Item::get(Item::ENDER_EYE, 2 , rand(1,3))->
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
		$hazekey = Item::get(Item::DYE, 9, rand(1,5));
		$hazekey->setCustomName("§r§d§lHaze §r§7Key (Right-Click)");
		$hazekey->setLore([
			"§r§7Right-Click this key on the §l§dHaze",
			"§r§l§dCrate §r§7located at §dspawn §7to obtain rewards.",
			"§r",
			"§r§dstore.buycraft.net"
		]);
		$hazekey->setNamedTagEntry(new ListTag("ench"));
		$hazekey->getNamedTag()->setTag(new StringTag("hazekeyxd"));

		$logger = Item::get(Item::EGG, 0 , rand(1,2))->
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


		$id = mt_rand(1,1000);
		$comma = "§r§8,";
		$partnerpackage = Item::get(Item::ENDER_CHEST, 0, rand(1,16))->
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

		$aegis = Item::get(Item::TRIPWIRE_HOOK, 0, 1);
		$aegis->setCustomName("§r§e§lAegis §r§7Key (Right-Click)");
		$aegis->setLore([
			"§r§7Right-Click this key on the §l§eAegis",
			"§r§l§eCrate §r§7located at §espawn §7to obtain rewards.",
			"§r",
			"§r§estore.buycraft.net"
		]);
		$aegis->setNamedTagEntry(new ListTag("ench"));
		$aegis->getNamedTag()->setTag(new StringTag("aegiskeyxd"));


		$sage = Item::get(Item::TRIPWIRE_HOOK, 0, 1);
		$sage->setCustomName("§r§5§lSage §r§7Key (Right-Click)");
		$sage->setLore([
			"§r§7Right-Click this key on the §l§5Spawn",
			"§r§l§5Crate §r§7located at §5spawn §7to obtain rewards.",
			"§r",
			"§r§5store.buycraft.net"
		]);
		$sage->setNamedTagEntry(new ListTag("ench"));
		$sage->getNamedTag()->setTag(new StringTag("sagekeyxd"));


		$abil = Item::get(Item::TRIPWIRE_HOOK, 0, rand(1,16));
		$abil->setCustomName("§r§c§lAbility §r§7Key (Right-Click)");
		$abil->setLore([
			"§r§7Right-Click this key on the §l§cAbility",
			"§r§l§cCrate §r§7located at §cspawn §7to obtain rewards.",
			"§r",
			"§r§cstore.buycraft.net"
		]);
		$abil->setNamedTagEntry(new ListTag("ench"));
		$abil->getNamedTag()->setTag(new StringTag("abilitykeyxd"));

		$goldblocks = Item::get(Item::GOLD_BLOCK,0,16);
		$gapples = Item::get(Item::GOLDEN_APPLE,0,rand(1,15));


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

		$helmet = Item::get(Item::DIAMOND_HELMET, 0 , 1);
		$helmet->setCustomName("§r§5§lPreview §r§7Helmet");
		$helmet->setLore([
			'§r§4Night Vision II'
		]);
		EnchantUtils::addEnchanmentToItem($helmet, ["Protection", "Unbreaking"], 2);
		$chestplate = Item::get(Item::DIAMOND_CHESTPLATE, 0, 1);
		EnchantUtils::addEnchanmentToItem($chestplate, ["Protection", "Unbreaking"], 2);
		$chestplate->setCustomName("§r§5§lPreview §r§7Chestplate");
		$chestplate->setLore([
			'§r§4Magma I',
		]);

		$leggings = Item::get(Item::DIAMOND_LEGGINGS, 0 , 1);
		$leggings->setCustomName("§r§5§lPreview §r§7Helmet");
		EnchantUtils::addEnchanmentToItem($leggings, ["Protection", "Unbreaking"], 2);
		$boots = Item::get(Item::DIAMOND_BOOTS, 0, 1);
		$boots->setCustomName("§r§5§lPreview §r§7Boots");
		$boots->setLore([
			'§r§4Speed II',
		]);
		EnchantUtils::addEnchanmentToItem($boots, ["Protection", "Unbreaking"],2);
		$sword = Item::get(Item::DIAMOND_SWORD);
		$sword->setCustomName("§r§5§lPreview  §r§7Sword");
		$sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS),2));
		$sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING),2));

		$pick = Item::get(Item::DIAMOND_PICKAXE);
		$pick->setCustomName("§r§5§lPreview  §r§7Pickaxe");
		$pick->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY),2));
		$pick->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING),2));

		$dia = Item::get(Item::DIAMOND_BLOCK, 0 ,16);

		$sage = Item::get(401, 0, rand(1,2));
		$sage->setCustomName("§r§l§5Sage Crate Key ALL §r§7(Right-Click)");
		$sage->setLore([
			"§r§7Right-Click to redeen this Key ALL voucher",
			"§r§7and give the whole server a §eFREE KEY!",
			"§r§7",
			"§r§l§cWARNING: §r§7everyone will love your",
			"§r§7after you use this Key ALL voucher!"
		]);

		$ability = Item::get(401, 0, rand(1,5));
		$ability->setCustomName("§r§l§cAbility Crate Key ALL §r§7(Right-Click)");
		$ability->setLore([
			"§r§7Right-Click to redeen this Key ALL voucher",
			"§r§7and give the whole server a §eFREE KEY!",
			"§r§7",
			"§r§l§cWARNING: §r§7everyone will love your",
			"§r§7after you use this Key ALL voucher!"
		]);

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

		$focusmode = Item::get(Item::GOLD_NUGGET, 0 , rand(1,6))->
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


		$decoy = Item::get(351, 7, rand(1,4))->
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

		$summer = Item::get(Item::CHEST, 1, rand(1,7));
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

		$sagel = Item::get(Item::CHEST, 1, rand(1,7));
		$sagel->setCustomName("§r§l§gSage Lootbox §r§7(Right-Click) §f(#0054)");
		$sagel->setNamedTagEntry(new ListTag("ench"));
		$sagel->getNamedTag()->setTag(new StringTag("lootbox_sage"));
		$sagel->setLore([
			"§r§7A exclusive item contained with great items",
			"§r§7this item is sacred and can be obtained on our store",
			"§r§7rewards (8-10)",
			"§r§6 * Sage Map 1 *",
			"",
			"§r§l§cNOTE: §r§7place to get compiled items"
		]);

		$menu->getInventory()->setItem(0,$ironBlocks); //0
		$menu->getInventory()->setItem(1,$summerob);
		$menu->getInventory()->setItem(2,$hazekey);
		$menu->getInventory()->setItem(3,$logger);
		$menu->getInventory()->setItem(4,$summerob);
		$menu->getInventory()->setItem(5,$partnerpackage);
		$menu->getInventory()->setItem(6,$aegis);
		$menu->getInventory()->setItem(7,$sage);
		$menu->getInventory()->setItem(8,$abil);
		$menu->getInventory()->setItem(9,$goldblocks);
		$menu->getInventory()->setItem(10,$gapples);
		$menu->getInventory()->setItem(11,$combo);
		$menu->getInventory()->setItem(12, $helmet);
		$menu->getInventory()->setItem(13, $chestplate);
		$menu->getInventory()->setItem(14, $leggings);
		$menu->getInventory()->setItem(15, $boots);
		$menu->getInventory()->setItem(16, $sword);
		$menu->getInventory()->setItem(17, $pick);
		$menu->getInventory()->setItem(18,$dia);
		$menu->getInventory()->setItem(19,$sage);
		$menu->getInventory()->setItem(20,$ability);
		$menu->getInventory()->setItem(21,$ninja);
		$menu->getInventory()->setItem(22,$focusmode);
		$menu->getInventory()->setItem(23,$decoy);
		$menu->getInventory()->setItem(24,$bone);
		$menu->getInventory()->setItem(25,$summer);
		$menu->getInventory()->setItem(26,$sagel);
		$menu->send($player);
	}
}