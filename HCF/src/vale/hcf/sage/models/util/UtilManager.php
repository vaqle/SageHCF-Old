<?php

namespace vale\hcf\sage\models\util;

use jojoe77777\FormAPI\ModalForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use vale\hcf\sage\provider\DataProvider;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\system\deathban\Deathban;
use vale\hcf\sage\system\ranks\RankAPI;

class UtilManager
{


	public static array $rankedSagePlayers = [];

	public static function createWith(SagePlayer $player): CompoundTag
	{
		$nbt = new CompoundTag("", [
			new ListTag("Pos", [
				new DoubleTag("", $player->x),
				new DoubleTag("", $player->y + $player->getEyeHeight()),
				new DoubleTag("", $player->z),
			]),
			new ListTag("Motion", [
				new DoubleTag("", -sin($player->yaw / 180 * M_PI) * cos($player->pitch / 180 * M_PI)),
				new DoubleTag("", -sin($player->pitch / 180 * M_PI)),
				new DoubleTag("", cos($player->yaw / 180 * M_PI) * cos($player->pitch / 180 * M_PI)),
			]),
			new ListTag("Rotation", [
				new FloatTag("", $player->yaw),
				new FloatTag("", $player->pitch),
			]),
		]);
		return $nbt;
	}

	public static function getSageRankedPlayers()
	{
		$onlines = Sage::getInstance()->getServer()->getOnlinePlayers();
		$ranked = "";

		foreach ($onlines as $player){
			$rank = DataProvider::$rankprovider->get($player->getName());
			if($player instanceof SagePlayer){
				if($rank === "Sage" || $rank  === "Cupid" || $rank === "Aegis" || $rank === "Raven"){
					$ranked.= $player->getName() . ", ";
				}
			}
		}
		return $ranked ?? "None";
	}


	public static function sendNotEnoughLives(SagePlayer $player){
		$form = new SimpleForm(function (SagePlayer $player,  $data) : void{
			if($data === null){
				return;
			}
			switch ($data){
				case 0:
					$lives = DataProvider::getLives($player->getName());
					if($lives >= 1){
						DataProvider::reduceLives($player->getName(),1);
						Deathban::setDeathbanned($player, 1);
						$player->getInventory()->clearAll();
						$player->getArmorInventory()->clearAll();
						DataProvider::setPvpTimer($player->getName(),1000);
					}else{
						$player->sendMessage("§l§c[!] §r§cIt appears that you have 0 §c§lLives §r§c “{$player->getName()}”\n§r§7((§r§o§7You can purchase lives on our store! ;D ))");

					}
					break;
			}
		});
		$form->setTitle("§r§7Confirmation");
		$form->setContent("§r§7Are you §4sure?");
		$form->addButton("§r§4- 1 Life.");
		$form->sendToPlayer($player);
	}



	public static function sendMainForm(SagePlayer $player){
		$form = new SimpleForm(function (SagePlayer $player, $data) : void{
			if($data === null){
				return;
			}
			switch ($data){
				case 0:
					self::sendNotEnoughLives($player);
					break;
				case 1:
					break;
			}
		});
		$form->setTitle("§4§lDeathbanned.");
		$lives = DataProvider::getLives($player->getName());
		$form->setContent("§r§4Deathbanned? §r§7Do you want to use a life? \n §r§a§lLives§r§a: §r§7{$lives}");
		$form->addButton("§r§4- 1 Life.");
		$form->addButton("§r§eIll wait.");
		$form->sendToPlayer($player);
	}



	public static function sendFirstReclaimForm(SagePlayer $p)
	{
		$form = new ModalForm(function (SagePlayer $p, $data): void {
			if ($data === null) {
				return;
			}
			switch ($data) {
				case 1:
					$p->sendMessage("§8      §6");

					$p->sendMessage("§8       §6");
					$p->sendMessage("§8      §6§lSageHCF     ");
					$p->sendMessage("§8  §6Welcome to Map §f#1-BETA");
					$p->sendMessage("§8 §b");
					$p->sendMessage("§l§eSTORE: §r§fTODO");
					$p->sendMessage("§l§eTWITTER: §r§fTODO");
					$p->sendMessage("§l§eDISCORD: §r§fTODO");
					$p->sendMessage("§8       §6");
					$p->sendMessage("§8       §6");
					$p->sendMessage("§r§7Welcome to the §6§lSageHCF §r§7Network, §r§7a friendly HCF Server \n§r§7that eliminates the pay to win feel and implements never seen before features.");
					$p->sendMessage("§r§aYour econimic bank has been created.");
					$p->sendMessage("§r§a§l+ 100$.");
					$p->sendMessage("§r§d+ 3x Haze Keys.");
					$p->sendMessage("§r§b 1x Air Drop.");
					$p->sendMessage("§r§4 1x Lootbox.");
					break;
			}
		});
		$form->setTitle("§6§lSageHCF §r§7Guide");
		$form->setContent("§r§7It appears that you are a new player on our network we have a couple of rules in place that you need to follow \n§r§f§l1. §r§7Cheating will not be tolerated. \n§r§f§l2. §r§7Racial Slurs will not be tolerated. \n§r§f§l3. §r§7Exploiting / duping is not tolerated.\n §r§f§l4. §r§7Evading of any Sort is not tolerated. \n §r§7Press accept to get your §r§6§lfirst join reclaim package.");
		$form->setButton1("§r§6Accept");
		$form->sendToPlayer($p);
	}
}