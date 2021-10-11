<?php

namespace vale\hcf\sage\commands\player;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\Plugin;
use vale\hcf\libaries\Command;
use vale\hcf\sage\kits\KitManager;
use vale\hcf\sage\models\util\UtilManager;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class SupportersCommand extends PluginCommand
{
	public $plugin;

	public function __construct(Sage $plugin)
	{
		parent::__construct("supporters", $plugin);
		$this->plugin = $plugin;
		$this->setDescription("§rrun this command to :\n" . str_repeat("§7 §4", 12) . "§r§l§2VIEW §rall sage supporters.");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if($sender instanceof SagePlayer){
			$rankedSage = UtilManager::getSageRankedPlayers();
			$sender->sendMessage("§r§5~ Online Supporters ~ \n §r§7Want your name to appear? Donate and recieve exclusive rewards! \n §r§d" . $rankedSage);
		}
		return true;
	}
}
