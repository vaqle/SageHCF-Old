<?php
namespace vale\hcf\sage\commands\staff;

use pocketmine\command\PluginCommand;
use pocketmine\command\{CommandSender, Command};
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

class DoubleVoteRewardsCommand extends PluginCommand
{
	public $plugin;

	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
		parent::__construct("doublevote", $plugin);
		$this->setDescription("§rrun this command to :\n" . str_repeat("§7 §4", 12) . "§r§l§aENABLE §ror §l§cDISABLE §rdouble vote rewards.");

	}

	public function execute(CommandSender $sender, string $label, array $args): bool
	{
		if (count($args) === 0 && $sender instanceof SagePlayer && $sender->isOp()){
			$sender->sendMessage("§r§e§l<§6X§e> §a§lDOUBLE VOTE REWARDS §r§e§l<§6X§e>");
			$sender->sendMessage("§6§l* §r§eon §r§7((enables event))");
			$sender->sendMessage("§6§l* §r§eoff §r§7((disables event))");
			return false;
		}

		if(!$sender->hasPermission("doublvote.cmd")){
			return false;
		}

		switch ($args[0]) {
			case "on":
				DataProvider::$fund->set("doublevote", "true");
				DataProvider::$fund->save();
				Sage::getInstance()->getServer()->broadcastMessage("§r§a§lDOUBLE VOTE REWARDS ACTIVE");
				Sage::getInstance()->getServer()->broadcastMessage("§r§7((Attention, everyone online will recieve double vote rewards for the next 24 hours");
				 Sage::getInstance()->getServer()->broadcastMessage("§r§7the more votes you have the more rewards you will recieve))");
				break;
			case "off":
				DataProvider::$fund->set("doublevote", "false");
				DataProvider::$fund->save();
				Sage::getInstance()->getServer()->broadcastMessage("§r§c§lDOUBLE VOTE REWARDS OFF");
				Sage::getInstance()->getServer()->broadcastMessage("§r§7((The X2 MULTIPLIER was turned off by a ADMIN))");
				break;
		}
		return true;
	}
}