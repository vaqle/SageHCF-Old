<?php

namespace vale\hcf\sage\waypoints\commands\subcommands;

use vale\hcf\libaries\SubCommand;
use vale\hcf\sage\SagePlayer;
use pocketmine\command\CommandSender;

class ListSubCommand extends SubCommand {

    /**
     * ListSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("list", "/waypoint list");
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
        $sender->sendMessage("WAY POINTS:");
        foreach($sender->getWayPoints() as $wayPoint) {
            $sender->sendMessage("wps" . $wayPoint->getName());
        }
    }
}