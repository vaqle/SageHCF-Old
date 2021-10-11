<?php

declare(strict_types = 1);

namespace vale\hcf\sage\commands\staff;

//commands
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginCommand;
//Player & Plugin
use pocketmine\Player;
use pocketmine\plugin\Plugin;
//level
use pocketmine\level\Level;
//utils
use pocketmine\utils\TextFormat;
//BASE
use vale\hcf\sage\Sage;
use vale\hcf\sage\system\ranks\RankAPI;
use vale\hcf\sage\provider\DataProvider as DataBase;

class SetRankCommand extends PluginCommand{

	const SET_RANK_SUCCESS = "§l§e(!) §r§eYour rank has now been set to a(n) §6";

	/**
	 * Heal constructor.
	 * @param Sage $plugin
	 */

	public function __construct($name, Sage $plugin) {

		parent::__construct($name, $plugin);
		$this->setDescription("§rallows command issuer to set a players rank");
		$this->setPermission("setrank.cmd");


	}

	/**
	 * @param CommandSender $sender
	 * @param string $label
	 * @param array $args
	 *
	 * @return bool
	 */

	public function execute(CommandSender $sender, string $label, array $args) {


		if($sender instanceof ConsoleCommandSender || $sender->hasPermission("setrank.cmd") || $sender->isOp()){
			if(count($args) < 1){
				$sender->sendMessage("§eInvalid arguement exceptions");
				$sender->sendMessage("§7usage: §e/setrank §7<§6player§7> <§6rank§7>");

			}elseif(($player = Sage::getInstance()->getServer()->getPlayer($args[0]))){
				if(isset($args[0]) && isset($args[1])){
					if($player instanceof Player && in_array($args[1], RankAPI::$Ranks)){
						DataBase::$rankprovider->set($player->getName(), $args[1]);
						DataBase::$rankprovider->save();
						$sender->sendMessage("§eYour rank transaction has been successfully completed");
						$sender->sendMessage("§7   §3");

						if($player->isOnline()){
							$player->sendMessage(self::SET_RANK_SUCCESS . $args[1]);
						}
					}elseif(!in_array($args[1], RankAPI::$Ranks)){
						$sender->sendMessage("§l§c(!) §r§cThe rank name you specified does not exist");
					}
				}
			}
		}else{
			$sender->sendMessage("§l§c(!) §r§cError: you lack sufficient permissions to access this command");
		}
	}

}