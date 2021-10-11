<?php

namespace vale\hcf\sage\commands\staff;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Server;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\system\messages\Messages;

class KingCommand extends PluginCommand {

	public $plugin;


	public function __construct(string $name, Sage $plugin)
	{
		parent::__construct($name, $plugin);
	}


	public function execute(CommandSender $sender, string $commandLabel, array $args): void
	{
		if (!$sender instanceof SagePlayer) {
			return;
		}

		if (!$sender->hasPermission("lo3o3koo3r3r,r.rrr3")) {
			return;
		}

		if (!isset($args[0])) {
			return;
		}
		if (!$p = Server::getInstance()->getPlayer($args[0])) {
			$sender->sendMessage(str_replace(["&", "{playerName}"], ["§", $args[0]], Messages::NOT_ONLINE));
			return;
		}
		$p = Server::getInstance()->getPlayer($args[0]);
		if ($p instanceof SagePlayer) {
			\vale\hcf\sage\system\events\KillTheKingEvent::setKing($p);
		}
	}
}