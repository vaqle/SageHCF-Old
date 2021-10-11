<?php

namespace vale\hcf\sage\waypoints\commands\subcommands;

use vale\hcf\libaries\SubCommand;
use vale\hcf\sage\SagePlayer;
use pocketmine\command\CommandSender;

class ToggleSubCommand extends SubCommand {

    /**
     * ToggleSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("toggle", "/waypoint toggle");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof SagePlayer) {
            $sender->sendMessage("no perms");
            return;
        }
        if($sender->isShowingWayPoint() === true) {
            $sender->setShowWayPoint(false);
            $text = $sender->getFloatingText("WayPoint");
            if($text !== null) {
                $sender->removeFloatingText("WayPoint");
                return;
            }
        }
        else {
            $sender->setShowWayPoint(true);
        }
        $sender->sendMessage("You toggled way point");
        return;
    }
}