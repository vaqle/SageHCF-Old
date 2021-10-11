<?php

namespace vale\hcf\sage\waypoints\tasks;

use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;
use pocketmine\level\Position;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class WayPointUpdateTask extends Task {
  
    public function onRun(int $currentTick) {
        /** @var SagePlayer $player */
        foreach(Sage::getInstance()->getServer()->getOnlinePlayers() as $player) {
            if((!$player->isOnline()) or ($player->getLevel() === null) or $player->isShowingWayPoint() === false) {
                return;
            }
            $message = [];
            foreach($player->getWayPoints() as $wayPoint) {
                if($wayPoint->getLevel()->getName() !== $player->getLevel()->getName()) {
                    continue;
                }
                $distance = floor($player->distance($wayPoint));
                $message[] = "§r§6[§r§c" . $wayPoint->getName() . "§r§6]" . "§r§6§l{$distance}m";
            }
            $text = $player->getFloatingText("WayPoint");
            if(empty($message) and $text !== null) {
                $player->removeFloatingText("WayPoint");
                return;
            }
            elseif(empty($message) and $text === null) {
                return;
            }
            $message = implode("\n", $message);
            $directionVector = $player->getDirectionVector()->multiply(2);

 $position = Position::fromObject($player->add($directionVector->getX(), $player->getEyeHeight(), $directionVector->getZ()), $player->getLevel());
            if($text === null) {
                $player->addFloatingText($position, "WayPoint", $message);
                return;
            }
            $text->update($message);
            $text->move($position);
            $text->sendChangesTo($player);
        }
    }
}