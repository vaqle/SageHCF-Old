<?php
namespace vale\hcf\sage\waypoints;
use pocketmine\level\Position;
use pocketmine\level\Level;

class Waypoint extends Position{
    
    public $name;
    public $x;
    public $y;
    public $z;
    public $level;
    
    public function __construct(string $name, int $x, int $y, int $z, Level $level){
        $this->name = $name;
        parent::__construct($x,$y,$z,$level);
    }
    
    public function getName(): string{
        return $this->name;
    }
}