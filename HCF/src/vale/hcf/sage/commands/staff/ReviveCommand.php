<?php

declare(strict_types = 1);

#   /\ /\___ _ __ __ _ ___
#   \ \ / / _ \ '__/ _`|/_\
#    \ V / __/ | | (_| |__/
#     \_/ \___ |_| \__,|\___
#                  |___/

namespace vale\hcf\sage\commands\staff;

//commands
use pocketmine\command\{ConsoleCommandSender, CommandSender};
use pocketmine\command\Command;
use pocketmine\command\PluginCommand;
//Player & Plugin & Server
use pocketmine\{Player, Server};
use pocketmine\plugin\Plugin;
//level
use pocketmine\level\Level;
//utils
use pocketmine\utils\TextFormat;
//Loader
use vale\hcf\sage\provider\DataProvider;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\partneritems\PItemManager;

class ReviveCommand extends PluginCommand
{

	/**
	 * KeyCommand constructor.
	 * @param Sage $plugin
	 */

	public function __construct($name, Sage $plugin)
	{

		parent::__construct($name, $plugin);
		$this->setDescription("§rA command to forcefully revive someone");
	}

	/**
	 * @param CommandSender $sender
	 * @param string $label
	 * @param array $args
	 *
	 * @return bool
	 */

	public function execute(CommandSender $sender, string $label, array $args)
	{


		if ($sender instanceof SagePlayer) {
			if ($sender->isOp() || $sender->hasPermission("revive.cmd") || $sender instanceof ConsoleCommandSender) {
				if (count($args) < 2) {
					$sender->sendMessage("§l§c[!] §r§cNo such player called “null” was found \n§7if you believe this is an error report this to vaqle");
					$sender->sendMessage("§r§e/reive <player> <amount>");
				} else {
					if (isset($args[0]) && ($player = Server::getInstance()->getPlayer($args[0])) && is_numeric($args[1])) {
						if ($player instanceof SagePlayer) {
							DataProvider::addLives($player->getName(), (int) $args[1]);
							$sender->sendMessage("§e§l(!) §r§eYou have successfully given §6x" . "Lives" . "§eto §6" . $player->getName());
						}
					} else {
						$sender->sendMessage("§l§c(!) §r§cYou lack sufficient permissions to access this command!");
					}
				}
			}
		}
		return true;
	}
}