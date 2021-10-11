<?php
namespace vale\hcf\sage\commands\staff;

use pocketmine\command\PluginCommand;
use pocketmine\command\{CommandSender, Command};
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TE;
use vale\hcf\sage\{handlers\events\SotwHandler,
	provider\DataProvider,
	Sage,
	SagePlayer,
	tasks\player\BarUpdateTask};
use vale\hcf\sage\system\events\FundEvent;
use vale\hcf\sage\tasks\player\CyberAttackQueueTask;

class LastInventoryCommand extends PluginCommand
{
	public $plugin;

	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
		parent::__construct("lastinventory", $plugin);
		$this->setDescription("§rrun this command to :\n" . str_repeat("§7 §4", 12) . "§r§l§5RESTORE §ra players inventory.");

	}

	public function execute(CommandSender $sender, string $label, array $args): bool
	{
		if ($sender->isOp() || $sender->hasPermission("staff.blacklist")) {
			if (isset($args[0])) {
				$p = Server::getInstance()->getPlayer($args[0]);
				if ($p instanceof SagePlayer) {
					if (DataProvider::revive($p)) {
						$sender->sendMessage("§l§e[!] §r§eYou succesfully restored §6§l“{$p->getName()}”s §r§eInventory. \n§7To reset there inventory again run the command.");
					} else {
						$sender->sendMessage("§l§e[!] §r§eWe could not find a §6§llast inventory §r§efor the player §6§l“{$p->getName()}”s §r§eInventory. \n§7To reset there inventory again run the command.");
					}
				} else {
					$sender->sendMessage("§l§e[!] §r§eWe could not find the player '{$args[0]}'. \n§7If you believe this is a error report it.");
				}
			} else {
				$sender->sendMessage("§6§lLAST INVENTORY");
				$sender->sendMessage("§r§6§l* §r§e/lastinventory §r§7- §r§6<player>");
			}
		}
		return true;
	}
}