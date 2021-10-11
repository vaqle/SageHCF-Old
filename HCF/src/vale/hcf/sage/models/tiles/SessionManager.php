<?php

namespace vale\hcf\sage\models\tiles;

use pocketmine\Player;

class SessionManager{

    /** @var Session[] */
    private $sessions = [];

    public function has(Player $Player): bool{
        return isset($this->sessions[$Player->getName()]);
    }

    public function get(Player $Player): Session{
        $this->add($Player);
        return $this->sessions[$Player->getName()];
    }

    public function add(Player $Player): void{
        if(!$this->has($Player)){
            $this->sessions[$Player->getName()] = new Session($Player);
        }
    }

    public function remove(Player $Player): void{
        if($this->has($Player)){
            unset($this->sessions[$Player->getName()]);
        }
    }
}