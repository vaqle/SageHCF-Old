<?php

declare(strict_types = 1);

#   /\ /\___ _ __ __ _ ___
#   \ \ / / _ \ '__/ _`|/_\
#    \ V / __/ | | (_| |__/
#     \_/ \___ |_| \__,|\___
#                  |___/

namespace vale\hcf\sage\system\monthlycrates\util;

//Base Libraries
use pocketmine\{Player, Server};
//Item Imports
use pocketmine\item\{enchantment\EnchantmentInstance, Item, enchantment\Enchantment};
//nbts
use pocketmine\nbt\tag\{CompoundTag, ListTag, StringTag, IntTag};
//CustomEnchants
use vale\hcf\sage\kits\KitManager;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\Sage;

class CrateUtils{


    public static $outsideGrid = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 15, 16, 17, 18, 19, 20, 24, 25, 26, 27, 28, 29, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 50, 51, 52, 53];
    public static $grid = [12, 13, 14, 21, 22, 23, 30, 31, 32];
    public static $bonus = 49;

    public static function getCrateTypeName(string $type): string{
        switch ($type) {
            case "july2019":
                return "§8Sage CRATE: June_2021";
                break;

        }
    }

    public static function getCrateTypeSurroundingGlass(string $type): \pocketmine\item\Item{
        switch($type) {
            case "july2019":
                $glass = \pocketmine\item\Item::get(241, 7, 1);
                $glass->setCustomName("§r ");
                return $glass;
                break;
        }
    }

    public static function setInventoryItems(\pocketmine\inventory\Inventory $inv, string $type): void{
        foreach(self::$outsideGrid as $outsideGrid){
            $inv->setItem($outsideGrid, self::getCrateTypeSurroundingGlass($type));
        }
        self::setGridItems($inv, $type);
        self::setBonusItem($inv);
    }

    public static function setGridItems(\pocketmine\inventory\Inventory $inv, string $type): void{
        $echest = \pocketmine\item\Item::get(130, 5, 1);
        $echest->setCustomName("§r§l§f???");
        $echest->setLore([
            "§r§7Click to redeem an item",
            "§r§7from this monthly crate"
        ]);
        $echest->setCustomBlockData(new CompoundTag("", [
                new StringTag("item-type", "slotgrid")
        ]));
        foreach(self::$grid as $grid){
            $inv->setItem($grid, $echest);
        }
    }

    public static function setBonusItem(\pocketmine\inventory\Inventory $inv): void{
        $echest = \pocketmine\item\Item::get(130, 1, 1);
        $echest->setCustomName("§r§l§c???");
        $echest->setLore([
            "§r§7You can't open the final reward",
            "§r§7until you have redeemed all other rewards"
        ]);
        $inv->setItem(self::$bonus, $echest);
    }

    public static function giveMonthlyCrate(Player $player, string $type, int $amount): void{
    $name = $player->getName();
    $cc = Item::get(130, 0, $amount);
                    switch($type) {
                        case "june2021":
                      $cc->setCustomName("§r§l§c*§f*§1* SAGE CRATE: §fJUNE §12021 §r§l§c*§f*§1* §r§7(Right-Click)");
                      $cc->setLore([
                        "§r§cUnlocked by §l$name §r§cat §1bit.ly/sagebuycraft",
                        "§r§l§fADMIN ITEM",
                        "§r§f* Knockback wand",
                        "",
                        "§r§l§eCOSMETIC ITEMS",
                        "§r§e* x1 Cape",
                        "§r§e* x1 Tag",
                        "",
                        "§r§l§6TREASURE ITEMS",
                        "§r§6* x1-3 Sage Lootboxes",
                        "§r§6* x2-5 Airdrops",
                        "§r§6* x1-10 Partner Packages",
                        "§r§6* x1-3 Portable Master Kits",
                        "§r§6* x1-5 Ability Keys",
                        "§r§6* x2-3 /skit Diamond Vouchers",
                        "§r§6* x1 /fix all permission",
                        "§r§6* x3-7 Sage Keys",
                        "§r§6* x1 Sharpness 3 FireAspect",
                        "§r§6and more...",
                        "",
                        "§r§l§1BONUS ITEMS",
                        "§r§1* Sage Rank Voucher §c(§lRARE§r§c)",
                        "§r§1* x1 June 2021 Monthly Crate §c(§lUNCOMMON§r§c)",
                        "§r§1* x25 Ability Keys §c(§lCOMMON§r§c)",
                    ]);
                    $player->getInventory()->addItem($cc);
                    $player->sendMessage("§l§e(!) §r§eYou've been given §6x$amount §6§lJUNE 2021 §r§eMonthly Crate(s)");
                    $player->sendMessage("§7Begin redeeming rewards by Right-Clicking the crate in hand...");
                            break;
                     
          /**  case "back2school2020":
                    $cc->setCustomName("§r§l§6*§7*§6* §7JERTEX CRATE: §6BACK TO SCHOOL... AGAIN! §r§l§6*§7*§6* §r§7(Right-Click)");
                    $cc->setLore([
                        "§r§6Unlocked by §l$name §r§6at §7Shop.JertexNetwork.net",
                        "§r§l§fADMIN ITEM",
                        "§r§f* Lightning wand",
                        "",
                        "§r§l§eCOSMETIC ITEMS",
                        "§r§e* x32-64 Space Fireworks",
                        "§r§e* 3-4 Item Nametags",
                        "",
                        "§r§l§6TREASURE ITEMS",
                        "§r§6* x1-3 Mystery Special Set Loootbox",
                        "§r§6* x20 Legendary Enchantment Books",
                        "§r§6* x1 Necromancer Mask",
                        "§r§6* x5 Godly Enchantment Books",
                        "§r§6* x1 Ares Boss egg",
                        "§r§6* x2-3 /gkit Warlock Demons",
                        "§r§6* x1 Tier 5 Money Pouch",
                        "§r§6* x3-7 Ultra Keys",
                        "§r§6* x1 Elite Key All flare",
                        "§r§6and more...",
                        "",
                        "§r§l§1BONUS ITEMS",
                        "§r§1* Paradox Rank Voucher §c(§lRARE§r§c)",
                        "§r§1* x1 July 2019 Monthly Crate §c(§lUNCOMMON§r§c)",
                        "§r§1* x15 Godly Keys §c(§lCOMMON§r§c)",
                    ]);
                    $player->getInventory()->addItem($cc);
                    $player->sendMessage("§l§e(!) §r§eYou've been given §6x$amount §6§lJULY 2019 §r§eMonthly Crate(s)");
                    $player->sendMessage("§7Begin redeeming rewards by Right-Clicking the crate in hand...");
                            break;
                            **/
                            
                    case "back2school2020":
                    $cc->setCustomName("§r§l§6*§7*§6* §7JERTEX CRATE: §6BACK TO SCHOOL... AGAIN! §r§l§6*§7*§6* §r§7(Right-Click)");
                    $cc->setLore([
                        "§r§6Unlocked by §l$name §r§6at §7https://shop.jertexnetwork.net",
                        "§r§l§fADMIN ITEM",
                        "§r§f* Skull of ItzRoboJoker",
                        "",
                        "§r§l§eCOSMETIC ITEMS",
                        "§r§e* 5-10 Item Nametags",
                        "",
                        "§r§l§6TREASURE ITEMS",
                        "§r§6* x1-6 Mystery Special Set Loootbox",
                        "§r§6* x10-15 Elite Enchantment Books",
                        "§r§6* x1-2 Heroic Paradox Chests",
                        "§r§6* x5 Godly Enchantment Books",
                        "§r§6* x1-3 Iron Golem Spawner(s)",
                        "§r§6* x2-3 /gkit Warlock Demons",
                        "§r§6* x1 Tier 4 Money Pouch",
                        "§r§6* x3-7 Ultra Keys",
                        "§r§6* x1 Legendary Key All flare",
                        "§r§6and more...",
                        "",
                        "§r§l§7BONUS ITEMS",
                        "§r§7* Ares Rank Voucher",
                        "§r§7* x1-4 Uluru Anvils",
                        "§r§7* x10 Legendary Keys",
                    ]);
                    $player->getInventory()->addItem($cc);
                    $player->sendMessage("§l§e(!) §r§eYou've been given §6x$amount §6§lBACK TO SCHOOL... AGAIN! §r§eMonthly Crate(s)");
                    $player->sendMessage("§7Begin redeeming rewards by Right-Clicking the crate in hand...");
                            break;
                                                                             
                     
                     
                     
                     

            }
    }

    public static function getCrateReward(\pocketmine\inventory\Inventory $inv, string $type): Item{
        switch($type) {
            case "july2019":

				$lol = Item::get(Item::DISPENSER, 0 ,rand(1,4));
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

				$partnerpackage = Item::get(Item::ENDER_CHEST, 0, rand(1,5))->
				setCustomName("§r§5§lPartner Package §r§f#1")
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

				$key1 = Item::get(Item::TRIPWIRE_HOOK, 0, rand(1,16));
				$key1->setCustomName("§r§5§lSage §r§7Key (Right-Click)");
				$key1->setLore([
					"§r§7Right-Click this key on the §l§5Spawn",
					"§r§l§5Crate §r§7located at §5spawn §7to obtain rewards.",
					"§r",
					"§r§5store.buycraft.net"
				]);
				$key1->setNamedTagEntry(new ListTag("ench"));
				$key1->getNamedTag()->setTag(new StringTag("sagekeyxd"));


				$summerob = Item::get(Item::ENDER_EYE, 2 , rand(1,6))->
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


				$key = Item::get(Item::TRIPWIRE_HOOK, 0, rand(1,19));
				$key->setCustomName("§r§c§lAbility §r§7Key (Right-Click)");
				$key->setLore([
					"§r§7Right-Click this key on the §l§cAbility",
					"§r§l§cCrate §r§7located at §cspawn §7to obtain rewards.",
					"§r",
					"§r§cstore.buycraft.net"
				]);
				$key->setNamedTagEntry(new ListTag("ench"));
				$key->getNamedTag()->setTag(new StringTag("abilitykeyxd"));

				$lasso  = Item::get(Item::LEAD, 0, rand(1,5))->
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


				$medkit  = Item::get(Item::DYE, 1, rand(1,7))->
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

				$strength  = Item::get(Item::BLAZE_POWDER, 0, rand(1,4))->
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


				$focusmode = Item::get(Item::GOLD_NUGGET, 0 , rand(1,20))->
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

				$helmet = Item::get(Item::DIAMOND_HELMET)->setCustomName("§r§8§l[§6§lBETA§8] §r§eHelemet");
				$helmet->setLore([
					'§r§4Night Vision II',
				]);
				$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));

				$chestplate = Item::get(Item::DIAMOND_CHESTPLATE, 0, 1);
				$chestplate->setCustomName("§r§8§l[§6§lBETA§8] §r§eChestplate");
				$chestplate->setLore([
					'§r§4Magma II',
					'§r§4Repair II',
				]);
				$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));

				$leggs = Item::get(Item::DIAMOND_LEGGINGS, 0, 1);
				$leggs->setCustomName("§r§8§l[§6§lBETA§8]  §r§eLeggings");
				$leggs->setLore([
					'§r§4Repair II',
				]);
				$leggs->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$leggs->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));

				$boots = Item::get(Item::DIAMOND_BOOTS, 0, 1);
				$boots->setCustomName("§r§8§l[§6§lBETA§8]  §r§eBoots");
				$boots->setLore([
					'§r§4Speed II',
				]);
				$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 2));
				$boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));


				$sword = Item::get(276,0,1);
				$sword->setCustomName("§r§4§lDemon Sword");
				$sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 3));
				$sword->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 2));

				$sage = Item::get(Item::CHEST, 1, rand(1,6));
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


				$summer = Item::get(Item::CHEST, 1, rand(1,6));
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

				$rewards = [$lol,$partnerpackage,$key1,$summerob,$key,$lasso,$medkit,$combo,$strength,$focusmode,$helmet,$chestplate,$leggs,$boots,$sword,$sage,$summer];
				$reward = $rewards[array_rand($rewards)];
				if($reward instanceof Item){
					return $reward;
				}else{
					#return new Item(Item::ENDER_PEARL);
				}
				break;
		}
	}

    public static function getBonusReward(string $type): Item{
        switch($type) {
            case "july2019":

                $cc = Item::get(130, 0, 1);
				$cc->setCustomName("§r§l§c*§f*§1* SAGE CRATE: §fJUNE §12021 §r§l§c*§f*§1* §r§7(Right-Click)");
				$cc->setLore([
					"§r§cUnlocked by §lnull §r§cat §1bit.ly/sagebuycraft",
					"§r§l§fADMIN ITEM",
					"§r§f* Knockback wand",
					"",
					"§r§l§eCOSMETIC ITEMS",
					"§r§e* x1 Cape",
					"§r§e* x1 Tag",
					"",
					"§r§l§6TREASURE ITEMS",
					"§r§6* x1-3 Sage Lootboxes",
					"§r§6* x2-5 Airdrops",
					"§r§6* x1-10 Partner Packages",
					"§r§6* x1-3 Portable Master Kits",
					"§r§6* x1-5 Ability Keys",
					"§r§6* x2-3 /skit Diamond Vouchers",
					"§r§6* x1 /fix all permission",
					"§r§6* x3-7 Sage Keys",
					"§r§6* x1 Sharpness 3 FireAspect",
					"§r§6and more...",
					"",
					"§r§l§1BONUS ITEMS",
					"§r§1* Sage Rank Voucher §c(§lRARE§r§c)",
					"§r§1* x1 June 2021 Monthly Crate §c(§lUNCOMMON§r§c)",
					"§r§1* x25 Ability Keys §c(§lCOMMON§r§c)",
				]);


				$sage = Item::get(Item::CHEST, 1, rand(1,6));
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


				$summer = Item::get(Item::CHEST, 1, rand(1,6));
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


                $rewards = [$cc,$summer,$sage];
$reward = $rewards[array_rand($rewards)];
        return $reward;

        }
    }

    public static function giveLootbox(string $lootbox, int $amount): Item{
        switch($lootbox){
            case "phantom":
            case "Phantom":
                $box = Item::get(54, 0, $amount);
                $box->setCustomName('§r§l§aSPECIAL SET "§fPhantom§a" §r§f(#0054)');
                $box->setLore([
                    '§7"A custom armor set to give',
                    '§7you the ultimate edge in battle."',
                    "§r",
                    "§r§l§aPHANTOM SET ITEMS:",
                    "§r§7* §fPhantom Helmet",
                    "§r§7* §fPhantom Chestplate",
                    "§r§7* §fPhantom Leggings",
                    "§r§7* §fPhantom Boots",
                    "§r§7* §fPhantom Blade",
                    "§r",
                    "§r§l§cWarning: §r§7We are not responsible if your",
                    "§r§7inventory is full and you do not recieve the items."
                ]);
                return $box;
                break;
            case "fantasy":
            case "Fantasy":
                $box = Item::get(54, 0, $amount);
                $box->setCustomName('§r§l§aSPECIAL SET "§fFantasy§a" §r§f(#0054)');
                $box->setLore([
                    '§7"A custom armor set to give',
                    '§7you the ultimate edge in battle."',
                    "§r",
                    "§r§l§aFANTASY SET ITEMS:",
                    "§r§7* §fFantasy Helmet",
                    "§r§7* §fFantasy Chestplate",
                    "§r§7* §fFantasy Leggings",
                    "§r§7* §fFantasy Boots",
                    "§r§7* §fFantasy Blade",
                    "§r",
                    "§r§l§cWarning: §r§7We are not responsible if your",
                    "§r§7inventory is full and you do not recieve the items."
                ]);
               return $box;

                break;
            case "traveller":
            case "Traveller":
                $box = Item::get(54, 0, $amount);
                $box->setCustomName('§r§l§aSPECIAL SET "§fTraveller§a" §r§f(#0054)');
                $box->setLore([
                    '§7"A custom armor set to give',
                    '§7you the ultimate edge in battle."',
                    "§r",
                    "§r§l§aTRAVELOR SET ITEMS:",
                    "§r§7* §fTraveller Helmet",
                    "§r§7* §fTraveller Chestplate",
                    "§r§7* §fTraveller Leggings",
                    "§r§7* §fTraveller Boots",
                    "§r",
                    "§r§l§cWarning: §r§7We are not responsible if your",
                    "§r§7inventory is full and you do not recieve the items."
                ]);
               return $box;

                break;
            case "yijiki":
            case "Yijiki":
                $box = Item::get(54, 0, $amount);
                $box->setCustomName('§r§l§aSPECIAL SET "§fYijiki§a" §r§f(#0054)');
                $box->setLore([
                    '§7"A custom armor set to give',
                    '§7you the ultimate edge in battle."',
                    "§r",
                    "§r§l§aYJIKI SET ITEMS:",
                    "§r§7* §fYjiki Helmet",
                    "§r§7* §fYjiki Chestplate",
                    "§r§7* §fYjiki Leggings",
                    "§r§7* §fYjiki Boots",
                    "§r§7* §fYjiki Blade",
                    "§r",
                    "§r§l§cWarning: §r§7We are not responsible if your",
                    "§r§7inventory is full and you do not recieve the items."
                ]);
                return $box;

                break;

            case "yeti":
            case "Yeti":
                $box = Item::get(54, 0, $amount);
                $box->setCustomName('§r§l§aSPECIAL SET "§fYeti§a" §r§f(#0054)');
                $box->setLore([
                    '§7"A custom armor set to give',
                    '§7you the ultimate edge in battle."',
                    "§r",
                    "§r§l§aYETI SET ITEMS:",
                    "§r§7* §fYeti Helmet",
                    "§r§7* §fYeti Chestplate",
                    "§r§7* §fYeti Leggings",
                    "§r§7* §fYeti Boots",
                    "§r§7* §fYeti Blade",
                    "§r",
                    "§r§l§cWarning: §r§7We are not responsible if your",
                    "§r§7inventory is full and you do not recieve the items."
                ]);
                return $box;
                break;
        }
    }


    public static function giveWarlock($warlock, int $amount): Item{
        switch($warlock){
            case "Paladin":
            case "paladin":
            case 1:
                $paladin = Item::get(Item::BONE, 1, $amount);
                $paladin->setCustomName("§r§l§fWARLOCK: §3Paladin Demon §r§7(Right-Click)");
                $paladin->setLore([
                    "§r§7Summons a warlock demon equiped",
                    "§r§7with the §l§3Paladin Gkit §r§7equipment.",
                    "§r",
                    "§r§7Defeat this warlock for a guaranteed",
                    "§r§7drop of the §f/gkit item set and a",
                    "§r§fchance §7to get a §f/gkit Redemption Shard",
                    "§r§7that can be used to unlock the §f/gkit",
                    "§r§7on your own account for regular use!",
                    "§r",
                    "§r§l§fUSE: §r§7To summon this warlock:",
                    "§r§l§fRight-Click §r§7this item inside the warzone.",
                    "§r",
                    "§r§l§cWARNING: §r§7Use this item wisely.",
                    "§r§7If lost it cannot be refunded"
                ]);

                return $paladin;
                break;
            case "Troll":
            case "troll":
            case 2:
                $troll = Item::get(Item::BONE, 2, $amount);
                $troll->setCustomName("§r§l§fWARLOCK: §3Troll Demon §r§7(Right-Click)");
                $troll->setLore([
                    "§r§7Summons a warlock demon equiped",
                    "§r§7with the §l§3Troll Gkit §r§7equipment.",
                    "§r",
                    "§r§7Defeat this warlock for a guaranteed",
                    "§r§7drop of the §f/gkit item set and a",
                    "§r§fchance §7to get a §f/gkit Redemption Shard",
                    "§r§7that can be used to unlock the §f/gkit",
                    "§r§7on your own account for regular use!",
                    "§r",
                    "§r§l§fUSE: §r§7To summon this warlock:",
                    "§r§l§fRight-Click §r§7this item inside the warzone.",
                    "§r",
                    "§r§l§cWARNING: §r§7Use this item wisely.",
                    "§r§7If lost it cannot be refunded"
                ]);

               return $troll;
                break;
            case "Necromancer":
            case "necromancer":
            case "necro":
            case 3:
                $necro = Item::get(Item::BONE, 3, $amount);
                $necro->setCustomName("§r§l§fWARLOCK: §9Necromancer Demon §r§7(Right-Click)");
                $necro->setLore([
                    "§r§7Summons a warlock demon equiped",
                    "§r§7with the §l§9Necromancer Gkit §r§7equipment.",
                    "§r",
                    "§r§7Defeat this warlock for a guaranteed",
                    "§r§7drop of the §f/gkit item set and a",
                    "§r§fchance §7to get a §f/gkit Redemption Shard",
                    "§r§7that can be used to unlock the §f/gkit",
                    "§r§7on your own account for regular use!",
                    "§r",
                    "§r§l§fUSE: §r§7To summon this warlock:",
                    "§r§l§fRight-Click §r§7this item inside the warzone.",
                    "§r",
                    "§r§l§cWARNING: §r§7Use this item wisely.",
                    "§r§7If lost it cannot be refunded"
                ]);

                return $necro;
                break;
            case "Pilgrim":
            case "pilgrim":
            case 4:
                $pilgrim = Item::get(Item::BONE, 4, $amount);
                $pilgrim->setCustomName("§r§l§fWARLOCK: §aPilgrim Demon §r§7(Right-Click)");
                $pilgrim->setLore([
                    "§r§7Summons a warlock demon equiped",
                    "§r§7with the §l§aPilgrim Gkit §r§7equipment.",
                    "§r",
                    "§r§7Defeat this warlock for a guaranteed",
                    "§r§7drop of the §f/gkit item set and a",
                    "§r§fchance §7to get a §f/gkit Redemption Shard",
                    "§r§7that can be used to unlock the §f/gkit",
                    "§r§7on your own account for regular use!",
                    "§r",
                    "§r§l§fUSE: §r§7To summon this warlock:",
                    "§r§l§fRight-Click §r§7this item inside the warzone.",
                    "§r",
                    "§r§l§cWARNING: §r§7Use this item wisely.",
                    "§r§7If lost it cannot be refunded"
                ]);

                return $pilgrim;
                break;
            case "Ogre":
            case "ogre":
            case 5:
                $ogre = Item::get(Item::BONE, 5, $amount);
                $ogre->setCustomName("§r§l§fWARLOCK: §2Ogre Demon §r§7(Right-Click)");
                $ogre->setLore([
                    "§r§7Summons a warlock demon equiped",
                    "§r§7with the §l§2Ogre Gkit §r§7equipment.",
                    "§r",
                    "§r§7Defeat this warlock for a guaranteed",
                    "§r§7drop of the §f/gkit item set and a",
                    "§r§fchance §7to get a §f/gkit Redemption Shard",
                    "§r§7that can be used to unlock the §f/gkit",
                    "§r§7on your own account for regular use!",
                    "§r",
                    "§r§l§fUSE: §r§7To summon this warlock:",
                    "§r§l§fRight-Click §r§7this item inside the warzone.",
                    "§r",
                    "§r§l§cWARNING: §r§7Use this item wisely.",
                    "§r§7If lost it cannot be refunded"
                ]);

                return $ogre;
                break;
            case "Phoenix":
            case "phoenix":
            case 6:
                $phoenix = Item::get(Item::BONE, 6, $amount);
                $phoenix->setCustomName("§r§l§fWARLOCK: §5Phoenix Demon §r§7(Right-Click)");
                $phoenix->setLore([
                    "§r§7Summons a warlock demon equiped",
                    "§r§7with the §l§5Phoenix Gkit §r§7equipment.",
                    "§r",
                    "§r§7Defeat this warlock for a guaranteed",
                    "§r§7drop of the §f/gkit item set and a",
                    "§r§fchance §7to get a §f/gkit Redemption Shard",
                    "§r§7that can be used to unlock the §f/gkit",
                    "§r§7on your own account for regular use!",
                    "§r",
                    "§r§l§fUSE: §r§7To summon this warlock:",
                    "§r§l§fRight-Click §r§7this item inside the warzone.",
                    "§r",
                    "§r§l§cWARNING: §r§7Use this item wisely.",
                    "§r§7If lost it cannot be refunded"
                ]);

                return $phoenix;
                break;
            case "Butcher":
            case "butcher":
            case 7:
                $butcher = Item::get(Item::BONE, 7, $amount);
                $butcher->setCustomName("§r§l§fWARLOCK: §4Butcher Demon §r§7(Right-Click)");
                $butcher->setLore([
                    "§r§7Summons a warlock demon equiped",
                    "§r§7with the §l§4Butcher Gkit §r§7equipment.",
                    "§r",
                    "§r§7Defeat this warlock for a guaranteed",
                    "§r§7drop of the §f/gkit item set and a",
                    "§r§fchance §7to get a §f/gkit Redemption Shard",
                    "§r§7that can be used to unlock the §f/gkit",
                    "§r§7on your own account for regular use!",
                    "§r",
                    "§r§l§fUSE: §r§7To summon this warlock:",
                    "§r§l§fRight-Click §r§7this item inside the warzone.",
                    "§r",
                    "§r§l§cWARNING: §r§7Use this item wisely.",
                    "§r§7If lost it cannot be refunded"
                ]);

               return $butcher;
                break;

        }
    }

    public static function addCE(Item $item, int $ceid): void{
      #  $ce = Server::getInstance()->getPluginManager()->getPlugin("CustomEnchantsAPI");
       # $ce->addEnchantment($item, $ceid, mt_rand(1, CustomEnchants::getEnchantment($ceid)->getMaxLevel(CustomEnchants::getEnchantment($ceid))));

    }
}