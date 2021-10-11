<?php

declare(strict_types = 1);


namespace vale\hcf\sage\floatingtext;

//Base Libraries
use vale\hcf\sage\factions\FactionsManager;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use pocketmine\level\particle\{FloatingTextParticle};
//DataBase
use vale\hcf\sage\provider\DataProvider as DataBase;
//vector3
use pocketmine\math\Vector3;

class TextManager
{

	public static $crate = [];
	public static $spawn_particles = [];
	public static $texts = [];


	public static function start(SagePlayer $player)
	{
		self::spawnCrateText($player);
	}

	public static function spawnCrateText(SagePlayer $player): void
	{
	    
	    $spawnx = -26;
		$spawny = 76;
		$spawnz = 20;
		$crates = self::$texts[$player->getName()]["crates"] = new FloatingTextParticle(new Vector3($spawnx + 0.5, $spawny + 2, $spawnz + 0.5), "");
		$crates->setTitle("§e§lCRATES §r§7 \n ((These Can Be Purchased On Our Buycraft or Obtained from KeyAlls/ Reclaims))");
		$player->getLevel()->addParticle($crates, [$player]);
	    
	    
	   	$spawnx = 18;
		$spawny = 75;
		$spawnz = -23;
		$shop = self::$texts[$player->getName()]["shop"] = new FloatingTextParticle(new Vector3($spawnx + 0.5, $spawny + 2, $spawnz + 0.5), "");
		$shop->setTitle("§c§lSHOP \n §r§7Buy and Sell Goods!");
		$player->getLevel()->addParticle($shop, [$player]);

		$spawnx = 1;
		$spawny = 77;
		$spawnz = 5;
		$info = self::$texts[$player->getName()]["info"] = new FloatingTextParticle(new Vector3($spawnx + 0.5, $spawny + 2, $spawnz + 0.5), "");
		$info->setTitle("§r§6§lSage\n§r§fWelcome to §r§eBETA 3.0 \n \n §r§7Started on the 25th of June.\n \n §r§6Map Kit§r§7: §r§fProtection 2, Sharpness 2 \n §r§6Team Size§r§7: §r§f8 Mans, 0 Allys\n \n §r§7§osage.hcf.club\n \n §r§7§ostore.sagehcf.club");
		$player->getLevel()->addParticle($info, [$player]);

		$x = 20;
		$y = 77;
		$z = 1;
		$topFactions = self::$texts[$player->getName()]["tpf"] = new FloatingTextParticle(new Vector3($x + 0.5, $y + 2, $z + 0.5), "");
		$topFactions->setTitle(Sage::getFactionsManager()->getFactionTopCrystals());
		$player->getLevel()->addParticle($topFactions, [$player]);

		$vx = 75;
		$vy = 73;
		$vz = -557;
		$cyber = self::$texts[$player->getName()]["cyber"] = new FloatingTextParticle(new Vector3($vx + 0.5, $vy + 2, $vz + 0.5), "");
		$cyber->setTitle("§r§6§lCyber Attack Event\n§7((Run down road to claim and §6§lfight foregien invaders§r§7! \n §r§7))\n§r\n§7Obtain op items keys and other items such as §r§7Air Drops§r§7, §r§7PartnerPackages§r§7, \n§r§7shop.sagehcf.net");
		$player->getLevel()->addParticle($cyber, [$player]);

		$cratedata = DataBase::$cratedata;
		/** Vote Crate **/
		if ($cratedata->exists("Haze-Crate")) {
			$vx = $cratedata->get("Haze-Crate")["x"];
			$vy = $cratedata->get("Haze-Crate")["y"];
			$vz = $cratedata->get("Haze-Crate")["z"];
			$haze = self::$crate[$player->getName()]["haze"] = new FloatingTextParticle(new Vector3($vx + 0.5, $vy + 2, $vz + 0.5), "");
			$haze->setTitle("§r§d§lHAZE CRATE \n§r\n§7((Right-click this to obtain §r§d§lHAZE CRATE \n  §r§7loot.))\n§r\n§7Obtain this key from crates from §r§d§lKey Alls§r§7, §r§d§lVoting§r§7, and\n§r§7Purchasing them via §r§d§lshop.sagehcf.net");
			$player->getLevel()->addParticle($haze, [$player]);

		}

		if ($cratedata->exists("Aegis-Crate")) {
			$vx = $cratedata->get("Aegis-Crate")["x"];
			$vy = $cratedata->get("Aegis-Crate")["y"];
			$vz = $cratedata->get("Aegis-Crate")["z"];
			$haze = self::$crate[$player->getName()]["haze"] = new FloatingTextParticle(new Vector3($vx + 0.5, $vy + 2, $vz + 0.5), "");
			$haze->setTitle("§r§e§lAEGIS CRATE \n§r\n§7((Right-click this to obtain §r§e§lAEGIS CRATE \n  §r§7loot.))\n§r\n§7Obtain this key from crates from §r§e§lKey Alls§r§7, §r§e§lVoting§r§7, and\n§r§7Purchasing them via §r§e§lshop.sagehcf.net");
			$player->getLevel()->addParticle($haze, [$player]);

		}

		if ($cratedata->exists("Sage-Crate")) {
			$vx = $cratedata->get("Sage-Crate")["x"];
			$vy = $cratedata->get("Sage-Crate")["y"];
			$vz = $cratedata->get("Sage-Crate")["z"];
			$haze = self::$crate[$player->getName()]["haze"] = new FloatingTextParticle(new Vector3($vx + 0.5, $vy + 2, $vz + 0.5), "");
			$haze->setTitle("§r§5§lSAGE CRATE \n§r\n§7((Right-click this to obtain §r§5§lSAGE CRATE \n  §r§7loot.))\n§r\n§7Obtain this key from crates from §r§5§lKey Alls§r§7, §r§5§lVoting§r§7, and\n§r§7Purchasing them via §r§5§lshop.sagehcf.net");
			$player->getLevel()->addParticle($haze, [$player]);

		}

		if ($cratedata->exists("Ability-Crate")) {
			$vx = $cratedata->get("Ability-Crate")["x"];
			$vy = $cratedata->get("Ability-Crate")["y"];
			$vz = $cratedata->get("Ability-Crate")["z"];
			$ability = self::$crate[$player->getName()]["ability"] = new FloatingTextParticle(new Vector3($vx + 0.5, $vy + 2, $vz + 0.5), "");
			$ability->setTitle("§r§c§lABILITY CRATE \n§r\n§7((Right-click this to obtain §r§c§lABILITY CRATE \n  §r§7loot.))\n§r\n§7Obtain this key from crates from §r§c§lKey Alls§r§7, §r§c§lVoting§r§7, and\n§r§7Purchasing them via §r§c§lshop.sagehcf.net");
			$player->getLevel()->addParticle($ability, [$player]);
		}

		if ($cratedata->exists("SummerOrb-Crate")) {
			$vx = $cratedata->get("SummerOrb-Crate")["x"];
			$vy = $cratedata->get("SummerOrb-Crate")["y"];
			$vz = $cratedata->get("SummerOrb-Crate")["z"];
			$summer = self::$crate[$player->getName()]["summer"] = new FloatingTextParticle(new Vector3($vx + 0.5, $vy + 2, $vz + 0.5), "");
			$summer->setTitle("§r§5§kejje  §r§d§lSUMMER 2.0 CRATE §r§5§kejje§r§d \n §r§fIncludes the new and §r§cbuffed chances§r§f! \n §r§fWin §r§d§lBIG §r§fprizes and other expensive items! \n §r§fPurchase now at §r§dstore.hcf.net§r§f. \n §f§l*§d§l*§f§l* §d§lRIGHT CLICK TO REDEEM §f§l*§d§l*§f§l* ");
			$player->getLevel()->addParticle($summer, [$player]);

		}
	}
}