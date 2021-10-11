<?php

namespace vale\hcf\sage\commands\player;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use vale\hcf\libaries\Command;
use vale\hcf\sage\kits\KitManager;
use vale\hcf\sage\provider\DataProvider;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\system\deathban\Deathban;
use vale\hcf\sage\system\messages\Messages;

class LivesCommand extends PluginCommand
{
	public $plugin;

	public function __construct(Sage $plugin)
	{
		parent::__construct("lives", $plugin);
		$this->plugin = $plugin;
		$this->setDescription("§rrun this command to :\n" . str_repeat("§7 §4", 12) . "§r§l§eVIEW §rand §l§eSEE §rlives of yourself or other players.");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if ($sender instanceof SagePlayer) {
			if (!isset($args[0])) {
				$lives = DataProvider::getLives($sender->getName());
				$sender->sendMessage("§r§eYour Lives: §r§6{$lives}");
				$sender->sendMessage("§r§e/lives [target] §r§7- Shows amount of lives that a player has");
				$sender->sendMessage("§r§e/revive <player> §r§7- Revives targeted player");
				return;
			}
		}
		if (!$p = Server::getInstance()->getPlayer($args[0])) {
			$sender->sendMessage(str_replace(["&", "{playerName}"], ["§", $args[0]], Messages::NOT_ONLINE));
			return;
		}
		$target = $p;
		$senderbal = $sender->getLives();
		$balance = DataProvider::getLives($target->getName());
		$sender->sendMessage("§r§6§l{$target->getName()} §r§chas $balance §r§6lives.");
	}
}