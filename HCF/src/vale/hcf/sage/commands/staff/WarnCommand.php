<?php
declare(strict_types=1);

namespace vale\hcf\sage\commands\staff;

use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class WarnCommand extends PluginCommand implements PluginIdentifiableCommand
{

	public Sage $plugin;
	public $name;

	public function __construct(string $name, Sage $plugin)
	{
		$this->name = $name;
		parent::__construct("warn", $plugin);
		$this->setPlugin($plugin);
	}


	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if (!$sender instanceof SagePlayer) {
			return;
		}
		if (!$sender->hasPermission("staff.blacklist")) {
			return;
		}
		if (!isset($args[0])) {
			$sender->sendMessage("§r§c[§l!§r§c] §r§cInvalid usage got args 'null' \n §r§7To §r§4§lunblacklist §r§7a player do /warn <name> <reason> ➰");
			return;
		}
		if (!isset($args[1])) {
			$sender->sendMessage("§r§cprovide reason");
			return;
		}
		if (!$p = Server::getInstance()->getPlayer($args[0])) {
			$sender->sendMessage("§r§cInvalid Player");
			return;
		}
		$reason = array_slice($args, 1);
		$p->sendPopup(str_repeat("",4) . "§r§4§lYou §r§chave been \n §4§lwarned §r§cby  {$sender->getName()} §r§cfor\n §4" . implode(" ", $reason));
		$p->getLevel()->addSound(new EndermanTeleportSound($p->getPosition()));
		Server::getInstance()->broadcastMessage("§r§4§l{$p->getName()} §r§chas been §4§lwarned §r§cby {$sender->getName()} §r§cfor §4" . implode(" ", $reason));
	}




	public function setPlugin(Sage $plugin): void
	{
		$this->plugin = $plugin;
	}

	public function getPlugin(): Plugin
	{
		return $this->plugin;
	}
}