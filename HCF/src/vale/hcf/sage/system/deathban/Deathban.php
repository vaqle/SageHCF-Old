<?php

namespace vale\hcf\sage\system\deathban;
use pocketmine\Player;
use pocketmine\utils\MainLogger;
use vale\hcf\sage\provider\DataProvider as DB;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\Sage;


class Deathban
{

	public string $playername;
	public int $time = 3200;
	public array $allDeathbanned = [];
	public static array $levels = ["deathban"];





	public static function initalizeDeathBanArenas()
	{
		$levels = count(self::$levels);
		for ($x = 0; $x < $levels; $x++) {
			Sage::getInstance()->getServer()->loadLevel(self::$levels[$x]);
			$loaded = self::$levels[$x];
			MainLogger::getLogger()->info("Preparing Levels {$loaded} for DeathBan Arena");
		}
	}

	public static function isDeathBanned(SagePlayer $player): bool{
		return DB::$deathbanned->exists($player->getName());
	}

	public static function setDeathbanned(SagePlayer $player, int $time)
	{
		DB::$deathbanned->set($player->getName(), $time);
		DB::$deathbanned->save();
	}

	public static function remove(SagePlayer $player){
		DB::$deathbanned->remove($player->getName());
		DB::$deathbanned->save();
	}


	public static function getDeathBanTime(SagePlayer $player): string{
		return DB::$deathbanned->get($player->getName());
	}
}