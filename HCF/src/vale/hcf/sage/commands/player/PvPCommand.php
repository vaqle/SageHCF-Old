<?php
namespace vale\hcf\sage\commands\player;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use vale\hcf\sage\provider\DataProvider;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class PvPCommand extends PluginCommand
{

	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
		parent::__construct("pvp", $plugin);
		$this->setDescription("§rrun this command to :\n" . str_repeat("§7 §4", 12) . "§r§l§bENABLE §rand §l§bDISABLE §ryour pvp timer");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if($sender instanceof SagePlayer){
			if(!isset($args[0])){
				$sender->sendMessage("§r§e§l<§6X§e> §r§6§lPvP Help §r§e§l<§6X§e>");
				$sender->sendMessage("§r§e§l* §r§7pvp enable ((Enable your pvp timer))");
				$sender->sendMessage("§r§6§l* §r§7pvp revive <player> ((Revive another player))");
				$sender->sendMessage("§r§e§l* §r§7pvp set ((Set another players pvp timer))");
				$sender->sendMessage("§r§6§l* §r§7pvp remove ((forecefully disable a pvptimer))");
				return false;
			}
			switch ($args[0]){
				case "enable":
					if(!$sender->hasPvpTimer()){
						$sender->sendMessage("§r§e* §r§7You do not have a current PvP Timer.");
					}elseif($sender->hasPvpTimer()){
						$sender->sendMessage("§r§e* §r§7You successfully enabled your PvP Timer.");
						DataProvider::setPvpTimer($sender->getName(), 0);
					}
					break;
			}
		}
		return true;
	}
}