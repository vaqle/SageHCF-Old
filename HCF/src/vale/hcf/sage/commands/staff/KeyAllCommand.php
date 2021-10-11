<?php

namespace vale\hcf\sage\commands\staff;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\Plugin;
use vale\hcf\sage\partneritems\PItemManager;
use vale\hcf\sage\provider\DataProvider;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\system\monthlycrates\util\CrateUtils;

class KeyAllCommand extends PluginCommand
{

	public $plugin;

	public function __construct(Sage $plugin)
	{
		$this->plugin = $plugin;
		parent::__construct("keyall", $plugin);
		$this->setPermission("keyall.cmd");
		$this->setDescription("§r§fdistribute keys to players.");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
	    if(!$sender->hasPermission("LOL")){
	        return false;
	    }
		if (!isset($args[0])) {
			$sender->sendMessage("§6§lKEY LIST");
			$sender->sendMessage("§r§7((/KEYALL <TYPE>))");
			$sender->sendMessage("§r§e§l* §r§7Aegis");
			$sender->sendMessage("§r§6§l* §r§7Sage");
			$sender->sendMessage("§r§e§l* §r§7Ability");
			$sender->sendMessage("§r§6§l* §r§7Haze");
			return;
		}
		switch ($args[0]) {
			case "Aegis":
				foreach (Sage::getInstance()->getServer()->getOnlinePlayers() as $player) {
					if ($player instanceof SagePlayer) {
						PItemManager::giveKeys($player, "Aegis", rand(1,2));
					}
					Sage::getInstance()->getServer()->broadcastMessage("§r§e(§e§l!§r§e) Everyone online has recieved §6§lAegis Keys.");
				}
				break;
				
			case "june":
				foreach (Sage::getInstance()->getServer()->getOnlinePlayers() as $player) {
					if ($player instanceof SagePlayer) {
						CrateUtils::giveMonthlyCrate($player, "june2021", (int)1);
					}
				}
				break;
			case "Summer":
				foreach (Sage::getInstance()->getServer()->getOnlinePlayers() as $player) {
					if ($player instanceof SagePlayer) {
						PItemManager::giveKeys($player, "Summer", rand(1,2));
					}
					Sage::getInstance()->getServer()->broadcastMessage("§r§e(§e§l!§r§e) Everyone online has recieved §6§lSummer Keys.");
				}
				break;

			case "Haze":
				foreach (Sage::getInstance()->getServer()->getOnlinePlayers() as $player) {
					if ($player instanceof SagePlayer) {
						PItemManager::giveKeys($player, "Haze", rand(1,2));
					}
					Sage::getInstance()->getServer()->broadcastMessage("§r§e(§e§l!§r§e) Everyone online has recieved §6§lHaze Keys.");

				}
				break;

			case "Ability":
				foreach (Sage::getInstance()->getServer()->getOnlinePlayers() as $player) {
					if ($player instanceof SagePlayer) {
						PItemManager::giveKeys($player, "Ability", rand(1,2));
					}
					Sage::getInstance()->getServer()->broadcastMessage("§r§e(§e§l!§r§e) Everyone online has recieved §6§lAbility Keys.");
				}
				break;

			case "Sage":
				foreach (Sage::getInstance()->getServer()->getOnlinePlayers() as $player) {
					if ($player instanceof SagePlayer) {
						PItemManager::giveKeys($player, "Sage", rand(1,2));
					}
					Sage::getInstance()->getServer()->broadcastMessage("§r§e(§e§l!§r§e) Everyone online has recieved §6§lSage Keys.");
				}
				break;

			case "Lootbox":
				foreach (Sage::getInstance()->getServer()->getOnlinePlayers() as $player) {
					if ($player instanceof SagePlayer) {
						PItemManager::giveLootBox($player, "Sage", rand(1,2));
					}
					Sage::getInstance()->getServer()->broadcastMessage("§r§e(§e§l!§r§e) Everyone online has recieved §6§lLootboxes.");
				}
				break;

			case "Package":
				foreach (Sage::getInstance()->getServer()->getOnlinePlayers() as $player) {
					if ($player instanceof SagePlayer) {
						PItemManager::givePartnerPackage($player, rand(1,2));
					}
					Sage::getInstance()->getServer()->broadcastMessage("§r§e(§e§l!§r§e) Everyone online has recieved §6§lPartner Packages.");
				}
				break;

			case "partner":
				foreach (Sage::getInstance()->getServer()->getOnlinePlayers() as $player) {
					if ($player instanceof SagePlayer) {
						DataProvider::addPartnerKeys($player->getName(), rand(1,3));
					}
				}
				Sage::getInstance()->getServer()->broadcastMessage("§r§e(§e§l!§r§e) Everyone online has recieved §6§lPartner Keys.");
				break;
				
		}
		return true;
	}
}