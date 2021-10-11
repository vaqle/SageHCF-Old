<?php
namespace vale\hcf\sage\commands\staff;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use vale\hcf\sage\partneritems\PItemListener;
use vale\hcf\sage\partneritems\PItemManager;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class MuteCommand extends PluginCommand implements PluginIdentifiableCommand
{

	public function __construct(Sage $plugin)
	{
		parent::__construct("mute", $plugin);
		$this->setDescription("§r§frun this command to" . str_repeat("§r§7", 5) . "§4§lMUTE §r§fother players.");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if ($sender instanceof SagePlayer) {
			if ($sender->isOp() || $sender->hasPermission("staff.blacklist") || $sender instanceof ConsoleCommandSender) {
				if (count($args) < 2) {
					$sender->sendMessage("§r§c[§l!§r§c] §r§cInvalid usage got args 'null' \n §r§7To §r§4§lmute §r§7a player do /mute <name> <time> ➰");

				} else {
					if (isset($args[0]) && ($player = Server::getInstance()->getPlayer($args[0])) &&  isset($args[1]) && is_numeric($args[1])) {
						if ($player instanceof SagePlayer) {
							Sage::getInstance()->getServer()->broadcastMessage("§r§c{$player->getName()} " .  "§cwas §l§4MUTED §r§cby {$sender->getName()}");
							Sage::getMuteProvider()->addMute($player->getName(),$args[1] + time());
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