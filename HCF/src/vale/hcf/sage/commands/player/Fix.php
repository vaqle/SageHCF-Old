<?php

declare(strict_types = 1);

 #   /\ /\___ _ __ __ _ ___
 #   \ \ / / _ \ '__/ _`|/_\
 #    \ V / __/ | | (_| |__/
 #     \_/ \___ |_| \__,|\___
 #                  |___/ 
 
namespace vale\hcf\sage\commands\player;

//commands
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\command\PluginCommand;
//Player & Plugin
use pocketmine\Player;
use pocketmine\plugin\Plugin;
//Level
use pocketmine\level\{Level, sound\AnvilUseSound};
//utils
use pocketmine\utils\TextFormat;
use pocketmine\item\Item;
//Loader
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class Fix extends PluginCommand{

	/**
	 * Heal constructor.
	 * @param Sage $plugin
	 */

	public function __construct($name, Sage $plugin) {
		parent::__construct($name, $plugin);
		$this->setDescription("§rrepairs your held item");
		$this->setPermission("fix.cmd");
		
	}

	/**
	 * @param CommandSender $sender
	 * @param string $label
	 * @param array $args
	 *
	 * @return bool
	 */

	public function execute(CommandSender $sender, string $label, array $args) {
	
  if(!$sender instanceof SagePlayer){
 $sender->sendMessage("only ingame players can run commands!");
	}
  if($sender instanceof Player && $sender->hasPermission("fix.cmd") || $sender->isOp()){
/** @var item **/
$hand = $sender->getInventory()->getItemInHand();
/** @var string **/
$name = $hand->getVanillaName();
$sender->sendMessage("§l§e(!) §r§eYou have successfully repaired your §6$name");
$hand->setDamage(0);
$sender->getInventory()->setItemInHand($hand);
$sender->getLevel()->addSound(new AnvilUseSound($sender));
      }else{
      $sender->sendMessage("§l§c(!) §r§cYou lack sufficient permissions to access this command");
     
    }
  }
}