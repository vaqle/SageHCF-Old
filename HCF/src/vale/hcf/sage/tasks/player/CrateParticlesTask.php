<?php
namespace vale\hcf\sage\tasks\player;

use pocketmine\level\particle\CriticalParticle;
use pocketmine\level\particle\DustParticle;
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\particle\HappyVillagerParticle;
use pocketmine\level\particle\HeartParticle;
use pocketmine\level\particle\RainSplashParticle;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use vale\hcf\sage\provider\DataProvider as DataBase;
use vale\hcf\sage\Sage;
use pocketmine\Server;
use vale\hcf\sage\SagePlayer;

class CrateParticlesTask extends Task
{

	public $plugin;


	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
	}

	public function onRun(int $currenttick)
	{
		$players = Sage::getInstance()->getServer()->getOnlinePlayers();
		$online = count(Server::getInstance()->getOnlinePlayers());
		if (count(Server::getInstance()->getOnlinePlayers()) > 0) {
			$cratedata = DataBase::$cratedata;
			if ($cratedata->exists("SummerOrb-Crate")) {
				$vx = $cratedata->get("SummerOrb-Crate")["x"];
				$vy = $cratedata->get("SummerOrb-Crate")["y"];
				$vz = $cratedata->get("SummerOrb-Crate")["z"];
				Sage::getInstance()->getServer()->getDefaultLevel()->addParticle(new HeartParticle(new Vector3($vx + rand(1, 2), $vy + rand(1,2), $vz + rand(1,2))));
			}

			if ($cratedata->exists("Sage-Crate")) {
				$vx = $cratedata->get("Sage-Crate")["x"];
				$vy = $cratedata->get("Sage-Crate")["y"];
				$vz = $cratedata->get("Sage-Crate")["z"];
				Sage::getInstance()->getServer()->getDefaultLevel()->addParticle(new CriticalParticle(new Vector3($vx + rand(1, 2), $vy + rand(1,2), $vz + rand(1,2))));
			}

			if ($cratedata->exists("Aegis-Crate")) {
				$vx = $cratedata->get("Aegis-Crate")["x"];
				$vy = $cratedata->get("Aegis-Crate")["y"];
				$vz = $cratedata->get("Aegis-Crate")["z"];
				Sage::getInstance()->getServer()->getDefaultLevel()->addParticle(new RainSplashParticle(new Vector3($vx + rand(1, 2), $vy + rand(1,2), $vz + rand(1,2))));
			}

			if ($cratedata->exists("Ability-Crate")) {
				$vx = $cratedata->get("Ability-Crate")["x"];
				$vy = $cratedata->get("Ability-Crate")["y"];
				$vz = $cratedata->get("Ability-Crate")["z"];
				Sage::getInstance()->getServer()->getDefaultLevel()->addParticle(new FlameParticle(new Vector3($vx + rand(1, 2), $vy + rand(1,2), $vz + rand(1,2))));
			}
			if ($cratedata->exists("Haze-Crate")) {
				$vx = $cratedata->get("Haze-Crate")["x"];
				$vy = $cratedata->get("Haze-Crate")["y"];
				$vz = $cratedata->get("Haze-Crate")["z"];
				Sage::getInstance()->getServer()->getDefaultLevel()->addParticle(new RainSplashParticle(new Vector3($vx + rand(1, 2), $vy + rand(1,2), $vz + rand(1,2))));
			}
		}
	}
}