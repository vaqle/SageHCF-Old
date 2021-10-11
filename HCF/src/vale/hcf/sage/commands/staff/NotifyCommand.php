<?php

declare(strict_types = 1);

namespace vale\hcf\sage\commands\staff;

//commands
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\command\PluginCommand;
//Player & Plugin & Server
use pocketmine\{Server, Player};
use pocketmine\plugin\Plugin;
//utils
use pocketmine\utils\TextFormat;
//Loader
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\Sage;

class NotifyCommand extends PluginCommand{

	const PREFIX = "§r§8[§e§k122§r§6§lSageHCF§r§e§k11§r§8]§e ";

	/**
	 * BroadcastCommand constructor.
	 * @param Sage $plugin
	 */

	public function __construct($name, Sage $plugin) {

		parent::__construct($name, $plugin);
		$this->setDescription("§rrun this announce things");
		$this->setPermission("bc.cmd");
		$this->setAliases(["bc", "announce"]);
	}

	/**
	 * @param CommandSender $sender
	 * @param string $label
	 * @param array $args
	 *
	 * @return bool
	 */

	public function execute(CommandSender $sender, string $label, array $args) {
		if($sender->isOp() || $sender->hasPermission("bc.cmd")){
			if(count($args) < 1){
				$sender->sendMessage("§r§c[§l!§r§c] §r§cInvalid usage got args 'null' \n §r§7To §r§4§lannounce §r§7a message do /notify <msg> ➰");
			}else{
				Server::getInstance()->broadcastMessage(self::PREFIX . implode(" ", $args));
			}
		}else{
			$sender->sendMessage("§l§c(!) §r§cYou lack sufficient permissions to access this command");
		}
	}
}