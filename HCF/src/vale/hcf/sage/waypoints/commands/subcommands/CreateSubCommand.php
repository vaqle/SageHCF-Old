<?php

namespace vale\hcf\sage\waypoints\commands\subcommands;

use vale\hcf\libaries\SubCommand;
use vale\hcf\sage\SagePlayer;
use pocketmine\command\CommandSender;
use vale\hcf\sage\waypoints\Waypoint;

class CreateSubCommand extends SubCommand {

    /**
     * CreateSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("create", "/waypoint create <name>");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof SagePlayer) {
            $sender->sendMessage("no perms");
            return;
        }
        if(count($sender->getWayPoints()) >= 5) {
            $sender->sendMessage("you have the max amount of waypoints please delete one");
            return;
        }
        if(!isset($args[1])) {
            $sender->sendMessage("usage" . $this->getUsage());
           
            return;
        }
        $name = (string)$args[1];
        if(strlen($name) > 16) {
            $sender->sendMessage("waypoint name to long");
            return;
        }
        if($sender->getWayPoint($name) !== null) {
            $sender->sendMessage("way point already exists");
            
            return;
        }
        $sender->sendMessage("created waypoint");
        $x = $sender->getFloorX();
        $y = $sender->getFloorY();
        $z = $sender->getFloorZ();
        $level = $sender->getLevel();
        $waypoint = new WayPoint($name, $x, $y, $z, $level);
        $sender->addWayPoint($waypoint);
        return;
    }
}