<?php

namespace vale\hcf\sage\commands\staff;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class UnblacklistCommand extends PluginCommand
{

	public Sage $plugin;

	public function __construct(string $name, Sage $plugin)
	{
		$this->plugin = $plugin;
		parent::__construct("unblacklist", $plugin);
		$this->setPermission("staff.blacklist");
		$this->setDescription("§r§frun this command to" . str_repeat("§r§7", 4) . " §r§4§lUNBLACKLIST §r§fa player.");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if (!isset($args[0])) {
			$sender->sendMessage("§r§c[§l!§r§c] §r§cInvalid usage got args 'null' \n §r§7To §r§4§lunblacklist §r§7a player do /unblacklist <name> ➰");
			return false;
		} elseif(!$sender->hasPermission("staff.blacklist")){
			return false;
		}elseif (isset($args[0])) {
			$sender->getServer()->getNameBans()->remove($args[0]);
			Sage::getInstance()->getServer()->broadcastMessage("§r§4{$args[0]} §r§cwas §r§l§4UN-BLACKLISTED §r§cby {$sender->getName()}");
		}
		return true;
	}
}

