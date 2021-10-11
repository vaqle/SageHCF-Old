<?php
namespace vale\hcf\sage\commands\staff;

use vale\hcf\sage\Sage;
use vale\hcf\sage\factions\FactionsManager;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use vale\hcf\sage\SagePlayer;


class AreaCommand extends PluginCommand {

	private $plugin;

	private $players = [];

	public function __construct(Sage $plugin) {

		parent::__construct("area", $plugin);

		$this->setPlugin($plugin);

	}
	public function setPlugin(Sage $plugin) {

		$this->plugin = $plugin;
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) {
		if($sender instanceof SagePlayer && $sender->isOp()){
			if(!isset($this->players[$sender->getName()])) {
				$this->players[$sender->getName()] = [
					"pos1" => null,
					"pos2" => null
				];
			}
			if(isset($args[0])) {
				switch($args[0]) {
					case "delete":
						Sage::getFactionsManager()->getDb()->exec("DELETE FROM claims WHERE faction = '$args[1]';");
						$sender->sendMessage("§r§7[§c§l!§r§7] §r§7Deleted the claim named {$args[1]}");
						break;
					case "pos1":
						$this->players[$sender->getName()]["pos1"] = $sender->getPosition();
						$sender->sendMessage("§r§7[§c§l!§r§7] §r§7Claiming position 1 has been set");
						$sender->sendMessage("§r§7[§c§l!§r§7] §r§7Please run the command /claim pos2");
						break;
					case "pos2":
						$this->players[$sender->getName()]["pos2"] = $sender->getPosition();
						$sender->sendMessage("§r§7[§c§l!§r§7] §r§7Claiming position 2 has been set");
						$sender->sendMessage("§r§7[§c§l!§r§7] §r§7Please run the command §r§c/claim confirm <type>");
						break;

					case "confirm":
						if(isset($args[1])) {
							switch($args[1]) {
								case "spawn":
									Sage::getFactionsManager()->claim("Spawn", $this->players[$sender->getName()]["pos1"], $this->players[$sender->getName()]["pos2"], FactionsManager::SPAWN);
									$sender->sendMessage("§r§7[§c§l!§r§7] §r§7Claimed the region succesfully");
									break;
								case "protected":
									Sage::getFactionsManager()->claim($args[1], $this->players[$sender->getName()]["pos1"], $this->players[$sender->getName()]["pos2"], FactionsManager::PROTECTED);
									$sender->sendMessage("§r§7[§c§l!§r§7] §r§7Claimed the region {$args[1]}");
									break;
								case "south":
									Sage::getFactionsManager()->claim("South Road", $this->players[$sender->getName()]["pos1"], $this->players[$sender->getName()]["pos2"], FactionsManager::SOUTH_ROAD);
									$sender->sendMessage("§r§7[§c§l!§r§7] §r§7Claimed the region succesfully");
									break;
								case "north":
									Sage::getFactionsManager()->claim("North Road", $this->players[$sender->getName()]["pos1"], $this->players[$sender->getName()]["pos2"], FactionsManager::NORTH_ROAD);
									$sender->sendMessage("§r§7[§c§l!§r§7] §r§7Claimed the region succesfully");
									break;
								case "west":
									Sage::getFactionsManager()->claim("West Road", $this->players[$sender->getName()]["pos1"], $this->players[$sender->getName()]["pos2"], FactionsManager::WEST_ROAD);
									$sender->sendMessage("§r§7[§c§l!§r§7] §r§7Claimed the region succesfully");
									break;
								case "east":
									Sage::getFactionsManager()->claim("East Road", $this->players[$sender->getName()]["pos1"], $this->players[$sender->getName()]["pos2"], FactionsManager::EAST_ROAD);
									$sender->sendMessage("§r§7[§c§l!§r§7] §r§7Claimed the region succesfully");
									break;

								case "deathban":
									Sage::getFactionsManager()->claim("Deathban arena", $this->players[$sender->getName()]["pos1"], $this->players[$sender->getName()]["pos2"], FactionsManager::DEATHBAN);
									$sender->sendMessage("scuces");
									break;

									case "cyber":
									   Sage::getFactionsManager()->claim("Cyber Attack", $this->players[$sender->getName()]["pos1"], $this->players[$sender->getName()]["pos2"], FactionsManager::CYBERATTACK);
									    break;

							}

						}

						break;

				}

			}

		}

	}

	public function getSage() : Sage {

		return $this->plugin;

	}

}