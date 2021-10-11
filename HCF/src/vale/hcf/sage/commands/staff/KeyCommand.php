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
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\partneritems\PItemManager;

class KeyCommand extends PluginCommand
{

	/**
	 * KeyCommand constructor.
	 * @param Sage $plugin
	 */

	public function __construct($name, Sage $plugin)
	{

		parent::__construct($name, $plugin);
		$this->setDescription("§rA command used to distribute specific keys");
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
			if ($sender->isOp() || $sender->hasPermission("lootbox.cmd") || $sender instanceof ConsoleCommandSender) {
				if (count($args) < 3) {
					$sender->sendMessage("§l§c[!] §r§cNo such key called “null” was found \n§7if you believe this is an error report this to vaqle");
					$sender->sendMessage("§r§e/key <player> <type> <amount>");
					$sender->sendMessage("§r§6KEY IDS: §r§eAbility, Sage, Haze, Summer, Aegis");
				} else {
					if (isset($args[0]) && ($player = Server::getInstance()->getPlayer($args[0])) && is_string($args[1]) && isset($args[2]) && is_numeric($args[2])) {
						if ($player instanceof SagePlayer) {
							PItemManager::giveKeys($player, (string)$args[1], (int)$args[2]);
							$sender->sendMessage("§e§l(!) §r§eYou have successfully given §6x" . $args[2] . " " . $args[1] . " Key(s) §eto §6" . $player->getName());
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