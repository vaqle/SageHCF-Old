<?php


namespace vale\hcf\sage\partneritems\tasks;


use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class LassoTask extends Task
{
	public $time = 30;

	public $damager;
	public $entity;

	public function __construct(SagePlayer $damager, SagePlayer $entity)
	{
		$this->damager = $damager;
		$this->entity = $entity;
	}

	public function getTime()
	{
		return $this->time;
	}

	public function getEntity()
	{
		return $this->entity;
	}

	public function getDamager()
	{
		return $this->damager;
	}

	public function onRun(int $currenttick)
	{
		--$this->time;

		if (!$this->damager->isOnline()) {
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
		}
		if (!$this->entity->isOnline()) {
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
		}

		if ($this->time === 28) {
			$dist =  9;
			$dir = $this->damager->subtract($this->entity);
			$dir = $dir->divide($dist);
			$yaw = atan2(-$dir->getX(),$dir->getZ());

			$x = cos($yaw);
			$z = sin($yaw);
			$this->entity->setMotion(new Vector3(-$x  - 3, 1, -$z));
		}

		if ($this->time === 25) {
			$dist =  9;
			$dir = $this->damager->subtract($this->entity);
			$dir = $dir->divide($dist);
			$yaw = atan2(-$dir->getX(),$dir->getZ());

			$x = cos($yaw);
			$z = sin($yaw);
			$this->entity->setMotion(new Vector3(-$x  + 1, 1, -$z));
		}


		if ($this->time === 22) {
			$dist =  9;
			$dist =  9;
			$dir = $this->damager->subtract($this->entity);
			$dir = $dir->divide($dist);
			$yaw = atan2(-$dir->getX(),$dir->getZ());

			$x = cos($yaw);
			$z = sin($yaw);
			$this->entity->setMotion(new Vector3(-$x  - 1, 1, -$z));
		}

		if ($this->time === 20) {
			$dist =  9;
			$dir = $this->damager->subtract($this->entity);
			$dir = $dir->divide($dist);
			$yaw = atan2(-$dir->getX(),$dir->getZ());

			$x = cos($yaw);
			$z = sin($yaw);
			$this->entity->setMotion(new Vector3(-$x  + 1, 1, -$z));
		}

		if ($this->time === 18) {
			$dist =  9;
			$dir = $this->damager->subtract($this->entity);
			$dir = $dir->divide($dist);
			$yaw = atan2(-$dir->getX(),$dir->getZ());

			$x = cos($yaw);
			$z = sin($yaw);
			$this->entity->setMotion(new Vector3(-$x  - 1, 1, -$z));
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
		}

	}
}
