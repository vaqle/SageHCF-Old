<?php

namespace vale\hcf\sage\waypoints\commands\subcommands;

use vale\hcf\libaries\SubCommand;
use vale\hcf\sage\SagePlayer;
use pocketmine\command\CommandSender;

class DeleteSubCommand extends SubCommand {

    /**
     * DeleteSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("delete", "/waypoint delete <name>");
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
            $sender->sendMessage("no perm");
            return;
        }
        if(!isset($args[1])) {
            $sender->sendMessage("usage" . $this->getUsage());
            
            return;
        }
        $name = (string)$args[1];
        if($sender->getWayPoint($name) === null) {
            $sender->sendMessage("waypoint dosent exist");
            return;
        }
        $sender->sendMessage("you deleted waypoint");
     
        $sender->removeWayPoint($name);
        return;
    }
}