<?php

namespace vale\hcf\sage\waypoints\commands;

use vale\hcf\libaries\Command;
use vale\hcf\sage\waypoints\commands\subcommands\CreateSubCommand;
use vale\hcf\sage\waypoints\commands\subcommands\DeleteSubCommand;
use vale\hcf\sage\waypoints\commands\subcommands\ListSubCommand;
use vale\hcf\sage\waypoints\commands\subcommands\ToggleSubCommand;
use pocketmine\command\CommandSender;

class WaypointCommand extends Command {

    /**
     * WayPointCommand constructor.
     */
    public function __construct() {
        $this->addSubCommand(new CreateSubCommand());
        $this->addSubCommand(new DeleteSubCommand());
        $this->addSubCommand(new ListSubCommand());
        $this->addSubCommand(new ToggleSubCommand());
        parent::__construct("waypoint", "Manage way points.", "/waypoint <toggle/create/delete/list>", ["wp"]);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(isset($args[0])) {
            $subCommand = $this->getSubCommand($args[0]);
            if($subCommand !== null) {
                $subCommand->execute($sender, $commandLabel, $args);
                return;
            }
            $sender->sendMessage("/waypoint <toggle> create delete list");
          
            return;
        }
       $sender->sendMessage("/waypoint <toggle> create delete list");

        return;
    }
}