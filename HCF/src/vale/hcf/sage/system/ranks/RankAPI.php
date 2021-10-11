<?php

declare(strict_types = 1);

#   /\ /\___ _ __ __ _ ___
#   \ \ / / _ \ '__/ _`|/_\
#    \ V / __/ | | (_| |__/
#     \_/ \___ |_| \__,|\___
#                  |___/

namespace vale\hcf\sage\system\ranks;

//Base Libraries
use pocketmine\{Player, Server};
use vale\hcf\sage\Sage;
use vale\hcf\sage\provider\DataProvider as DB;
//Item
use pocketmine\item\Item;
use vale\hcf\sage\SagePlayer;

class RankAPI{

	const SET_RANK_SUCCESS = "§l§e(!) §r§eYour rank has now been set to a(n) §6";
//const
	public static $Ranks = ["Aesthete", "Sage", "Cupid", "Trial", "Aegis", "Partner", "Media", "Famous", "Mod", "Admin", "Raven", "Booster"];
	public static $instance;

	/**
	 * @param setRank() @var $player, $rank
	 * Sets a player's ingame Rank/Group
	 */

	public static function setRank(SagePlayer $player, string $rank): bool{
		if(in_array($rank, self::$Ranks)){
			DB::$rankprovider->set($player->getName(), $rank);
			DB::$rankprovider->save();
			if($player->isOnline()){
			
				$player->sendMessage(self::SET_RANK_SUCCESS . $rank);
			}
			return true;
		}elseif(!in_array($rank, self::Ranks)){
			return false;
		}
	}

	/**
	 * @param delRank() @var $rank
	 * removes a specific rank from the database
	 */

	public static function delRank(string $rank): bool{
		if(DB::$ranks->exists($rank)){
			DB::$ranks->remove($rank);
			return true;
		}else{
			return false;
		}
	}

	/**
	 * @param addRank() @var $rank
	 * Incorporate's your desired rank into the database
	 */

	public static function addRank(string $rank): bool{
		if(DB::$ranks->exists($rank)){
			return false;
		}elseif(!DB::$ranks->exists($rank)){
			DB::$ranks->set($rank);
			DB::$ranks->save();
			return true;
		}
	}

	/**
	 * @param initRanks
	 * This simply initiates the ingame role assignment
	 * to recently created player profiles
	 */

	public static function initRanks(SagePlayer $player): void{
		if(!DB::$rankprovider->exists($player->getName())){
			DB::$rankprovider->set($player->getName(), "Aesthete");
		}
	}

	public static function hasRank(Player $player, string $rank): bool{
		if(DB::$rankprovider->get($player->getName()) == $rank){
			return true;
		}else{
			return false;
		}
	}

	public static function getRank(SagePlayer $player): string{
		return DB::$rankprovider->get($player->getName());
	}

	/**  public static function giveRank(int $rankid): void{
	switch($rankid){

	case 2:
	$pos = Item::get(Item::PAPER, 1, 1);
	$pos->setCustomName("§r§l§eRANK");
	break;
	case 3:
	$dweller
	break;
	case 4:
	$dweller
	break;
	case 5:
	$dweller
	break;
	case 6:
	$dweller
	break;
	case 7:
	$dweller
	break;
	case 8:
	$dweller
	break;
	case 9:
	$dweller
	break;
	case 10:
	$dweller
	break;
	case 11:
	$dweller
	break;
	}
	}*/
}