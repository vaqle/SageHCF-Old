<?php

namespace vale\hcf\sage\models\tiles;

use vale\hcf\sage\models\tiles\FakeBlockInventory;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;

class Session{

    private $Player;
    
    private $currentWindow = null;

    public function __construct(Player $Player){
        $this->Player = $Player;
    }

    public function getCurrentWindow(): ?FakeBlockInventory{
        return $this->currentWindow;
    }

    public function setCurrentWindow(?FakeBlockInventory $currentWindow): void{
        $this->currentWindow = $currentWindow;
    }

    /**
     * @param Player|Vector3 $Player
     * @param string $sound
     * @param float $pitch
     * @param float $volume
     * @param bool $packet
     * @return DataPacket|null
     */
    public static function playSound($Player, string $sound, float $pitch = 1, float $volume = 1, bool $packet = false): ?DataPacket{
        $pk = new PlaySoundPacket();
        $pk->soundName = $sound;
        $pk->x = $Player->x;
        $pk->y = $Player->y;
        $pk->z = $Player->z;
        $pk->pitch = $pitch;
        $pk->volume = $volume;
        if($packet){
            return $pk;
        }elseif($Player instanceof Player){
            $Player->dataPacket($pk);
        }
        return null;
    }
}