<?php

declare(strict_types = 1);

 #   /\ /\___ _ __ __ _ ___
 #   \ \ / / _ \ '__/ _`|/_\
 #    \ V / __/ | | (_| |__/
 #     \_/ \___ |_| \__,|\___
 #                  |___/ 

namespace vale\hcf\sage\models\entitys;

use pocketmine\{
    Player, Server
};
use pocketmine\entity\{
    Human, Entity, Zombie, Monster, Living
};
use pocketmine\level\{Level, Position};
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;

class TextEntity extends Entity{

public const NETWORK_ID = self::CAT;
/** @var float **/
public $height = 0.7;
/** @var float **/
public $width = 0.6;

/** @var float **/
protected $gravity = 0.00;

private static $life_tick;

  public function __construct(Level $level, CompoundTag $nbt, int $tick = 100){
     parent::__construct($level, $nbt);
         self::$life_tick = $tick;
  
  }


/** Spawns the popup
  * @var Position|$pos
  * @var string|$text
  *
  */
  public static function spawnText(Position $pos, string $text, int $newlife = 100) : self{
 
     $entity = new self($pos->getLevel(), self::createBaseNBT($pos), $newlife);
     $entity->setNametag($text);
    # $entity->setNametagAlwaysVisible(true);
     $entity->spawnToAll();
     return $entity;
    
  }
  
  /**
    * @return string
    */

  public function getName() : string{
     return "popup text";
  }
  
  protected function initEntity() : void{
    $this->setScale(0.0001);
    $this->setImmobile(true);
    $this->setCanSaveWithChunk(false);
    $this->setNameTagAlwaysVisible(true);
  
  }
  
  public function entityBaseTick(int $tickDiff = 100) : bool{
  $this->motion->setComponents(0, 0, 0);
      if(self::$life_tick > 0) self::$life_tick--;
      if(self::$life_tick <= 0 && !$this->isFlaggedForDespawn()) $this->flagForDespawn();
  return parent::entityBaseTick($tickDiff);
  }
}