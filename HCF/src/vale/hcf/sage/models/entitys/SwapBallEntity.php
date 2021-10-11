<?php
declare(strict_types=1);

namespace vale\hcf\sage\models\entitys;

use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Snowball;
use pocketmine\math\RayTraceResult;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class SwapBallEntity extends Snowball {

	protected function onHitEntity(Entity $entityHit, RayTraceResult $hitResult) : void{
		$thrower = $this->getOwningEntity();
		if(!$thrower instanceof SagePlayer || !$entityHit instanceof SagePlayer){
			$this->flagForDespawn();
			return;
		}
		if($thrower->distanceSquared($entityHit) > 144){
			$this->flagForDespawn();
			return;
		}
		$mgr = Sage::getFactionsManager();
		if($entityHit instanceof SagePlayer && $mgr->isSpawnClaim($entityHit)){
			$this->flagForDespawn();
			return;
		}
		if($thrower instanceof SagePlayer && $mgr->isSpawnClaim($thrower)){
			$this->flagForDespawn();
			return;
		}
		$throwerPos = $thrower->asPosition();
		$thrower->teleport($entityHit);
		$entityHit->teleport($throwerPos);
		$this->flagForDespawn();
	}
}
