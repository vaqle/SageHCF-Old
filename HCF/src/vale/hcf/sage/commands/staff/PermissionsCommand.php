<?php
declare(strict_types=1);

namespace hcf\commands\staff;

use hcf\{
   AlpineCore, AlpinePlayer
};
use pocketmine\command\{
   CommandSender, PluginCommand
};
use pocketmine\item\Item;
use pocketmine\nbt\tag\{CompoundTag, ByteTag, ListTag};
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class PermissionsCmd extends PluginCommand {
    /** @var AlpineCore */
    private $plugin;
    /**
      * PermissionsCmd constructor.
      *
      * @param AlpineCore $plugin
      */
    public function __construct(AlpineCore $plugin){
        parent::__construct("permission", $plugin);
        $this->plugin = $plugin;
        $this->setPermission("core.cmd.permission");
        $this->setUsage("/permission <add|remove> [permission] [username]");
        $this->setAliases(["perm", "perms"]);
        $this->setDescription("Give or take permissions from a player!");
    }
   
    /**
      * @param CommandSender $sender
      * @param string $commandLabel
      * @param array $args
      */
    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender->hasPermission("core.cmd.permission")){
            $sender->sendMessage("". "§l§c»» §r§7You do not have permission to run this command!");
            return;
        }
        if(empty($args)){
            $sender->sendMessage("§l§c»» §r§7Use '/permission <add|remove> [permission] [username]'");
            return;
        }
        if(!isset($args[2])){
           $sender->sendMessage("§l§c»» §r§7Use '/permission <add|remove> [permission] [username]'");
            return;
        }
        if(!isset($args[1])){
           $sender->sendMessage("§l§c»» §r§7Use '/permission <add|remove> [permission] [username]'");
            return;
        }
        if(($target = $this->plugin->getServer()->getPlayer($args[2])) === null){
            $sender->sendMessage("§l§c»» §r§7That player was not found!");
            return;
        }
        if(isset($args[0])){
            if($args[0] == "add"){
                $permission = (string) $args[1];
                $target->addPermission($permission);
                $sender->sendMessage("You have added permission to " . $target->getName() . ": " . $args[1]);
                return;
            } elseif($args[0] == "remove"){
                $permission = (string) $args[1];
                $target->removePermission($permission);
                $sender->sendMessage("You have removed permission from " . $target->getName() . ": " . $args[1]);
                return;
            }
        }
    }
}