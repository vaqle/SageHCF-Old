<?php

declare(strict_types = 1);

#   /\ /\___ _ __ __ _ ___
#   \ \ / / _ \ '__/ _`|/_\
#    \ V / __/ | | (_| |__/
#     \_/ \___ |_| \__,|\___
#                  |___/

namespace vale\hcf\sage\system\monthlycrates\command;

//commands
use pocketmine\command\{ConsoleCommandSender, CommandSender};
use pocketmine\command\Command;
use pocketmine\command\PluginCommand;
//Player & Plugin & Server
use pocketmine\{Server, Player};
use pocketmine\plugin\Plugin;
//level
use pocketmine\level\Level;
//utils
use pocketmine\utils\TextFormat;
//Loader
use vale\hcf\sage\system\monthlycrates\util\CrateUtils;
use vale\hcf\sage\Sage;
use vale\hcf\sage\system\monthlycrates\MonthlyCrates;

class MonthlyCrateCommand extends PluginCommand{

	/**
	 * MonthlyCrate constructor.
	 * @param MonthlyCrates $plugin
	 */

	public function __construct(Sage $plugin) {

		parent::__construct("mc", $plugin);
		$this->setDescription("§rallows the command issuer to give monthly crates");
		$this->setPermission("mc.cmd");
		$this->setAliases(["mc"]);

	}

	/**
	 * @param CommandSender $sender
	 * @param string $label
	 * @param array $args
	 *
	 * @return bool
	 */

	public function execute(CommandSender $sender, string $label, array $args) {


		if($sender instanceof Player || $sender instanceof ConsoleCommandSender){
			if($sender->hasPermission("mc.cmd") || $sender->isOp()){
				if(count($args) < 3){
					$sender->sendMessage("§eInvalid arguement exceptions...");
					$sender->sendMessage("§7Usage: §e/monthlycrate(mc) §7<§6player§7> <§6type§7> <§6amount§7>");
				}elseif(($player = Server::getInstance()->getPlayer($args[0])) && is_string($args[1]) && is_numeric($args[2])){
					$name = $player->getName();
					CrateUtils::giveMonthlyCrate($player, (string)$args[1], (int)$args[2]);
				}
			}else{

			}
		}
	}
}