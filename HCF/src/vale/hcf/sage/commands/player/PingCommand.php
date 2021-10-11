<?php

namespace vale\hcf\sage\commands\player;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\Plugin;
use vale\hcf\libaries\Command;
use vale\hcf\sage\kits\KitManager;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\system\events\KillTheKingEvent;

class PingCommand extends PluginCommand
{
	public $plugin;

	public function __construct(Sage $plugin)
	{
		parent::__construct("ping", $plugin);
		$this->plugin = $plugin;
		$this->setDescription("§rrun this command to :\n" . str_repeat("§7 §4", 12) . "§r§l§6SEE §ryour §rms");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if($sender instanceof SagePlayer){
            $ping = $sender->getPing();
            $sender->sendMessage("§r§eYour current MS: §r§6{$ping}");
           # KillTheKingEvent::setKing($sender);
		}
		return true;
	}
}

