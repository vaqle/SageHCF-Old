<?php

namespace vale\hcf\sage\commands\staff;

use vale\hcf\sage\Sage;
use vale\hcf\sage\handlers\events\SotwHandler;

use pocketmine\command\{CommandSender, PluginCommand};
use pocketmine\utils\TextFormat as TE;

class SotwCommand extends PluginCommand {

    /**
     * SOTWCommand Constructor.
     */
    public function __construct(){
        parent::__construct("sotw", Sage::getInstance());
    }

    /**
     * @param CommandSender $sender
     * @param String $label
     * @param array $args
     * @return void
     */
    public function execute(CommandSender $sender, String $label, Array $args) : void {
        if(count($args) === 0){
            $sender->sendMessage("");
            return;
        }
        if(!$sender->isOp()){
            $sender->sendMessage(TE::RED."§r§7((TIP: §r§cYou must be a operator to use this command §r§7))");
            return;
        }
        switch($args[0]){
            case "on":
                if(!$sender->isOp()){
                    $sender->sendMessage(TE::RED."§r§7((TIP: §r§cYou must be a operator to use this command §r§7))");
                    return;
                }
                if(empty($args[1])){
                    $sender->sendMessage("§r§7((TIP:/sotw §r§ctime §r§7[Int: time]");
                    return;
                }
                if(SotwHandler::isEnable()){
                    $sender->sendMessage(TE::RED."§r§7((TIP: §r§cSotw is Already Running§r§7))");
                    return;
                }
                SotwHandler::start($args[1]);
                break;

            case "add":
                if(!$sender->isOp()){
                    $sender->sendMessage(TE::RED."§r§7((TIP: §r§cYou must be a operator to use this command §r§7))");
                    return;
                }
                if(SotwHandler::isEnable() && !empty($args[1])){
                    SotwHandler::setTime(SotwHandler::getTime() + $args[1]);
                }
                $sender->sendMessage("§r§cAdded" . Sage::getTimeToFullString($args[1])  ."to SOTW");

                break;
            case "off":
                if(!$sender->isOp()){
                    $sender->sendMessage(TE::RED."§r§7((TIP: §r§cYou must be a operator to use this command §r§7))");

                    return;
                }
                if(!SotwHandler::isEnable()){
                    $sender->sendMessage(TE::RED."§r§7((TIP: §r§cStart of the World has to be on§r§7))");
                    return;
                }
                SotwHandler::stop();
                break;
        }
    }
}