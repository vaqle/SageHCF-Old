<?php
declare(strict_types=1);
namespace vale\hcf\sage\system\classes;

use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\Sage;
use pocketmine\{
	event\Listener,
	utils\TextFormat
};
use pocketmine\entity\{
	projectile\Arrow, Entity
};
use pocketmine\event\entity\{
	EntityDamageEvent,
	EntityDamageByChildEntityEvent
};
use vale\hcf\sage\tasks\player\ArcherTagTask;

class ArcherClass implements Listener{

	private $plugin;
	public function __construct(Sage $plugin){
		$this->plugin = $plugin;
	}

	/**
	 * @param EntityDamageEvent $event
	 */
	public function onDamage(EntityDamageEvent $event){
		if($event instanceof EntityDamageByChildEntityEvent) {
			$child = $event->getChild();
			if ($child instanceof Arrow) {
				$entity = $event->getEntity();
				$shoot = $event->getDamager();
				if ($entity instanceof SagePlayer && $shoot instanceof SagePlayer) {
					if ($shoot->isArcher()) {
						$mgr = Sage::getFactionsManager();
						if ($mgr->isSpawnClaim($entity)) {
							$event->setCancelled(true);
						}
						if ($mgr->isSpawnClaim($shoot)) {
							$event->setCancelled(true);
						}

						if ($event->isCancelled()) {
							if($entity->isArcherTagged()) {
								$this->plugin->getScheduler()->scheduleRepeatingTask(new ArcherTagTask($entity), 20);
								$entity->setArchertagTime(25);
								$heart = $entity->getHealth();
								$entity->setHealth($heart - rand(1, 3));
								$shoot->sendMessage("taggd player");
								$entity->sendMessage("archer tagged");
							} else {
								$entity->setArchertagTime(25);
								$this->plugin->getScheduler()->scheduleRepeatingTask(new ArcherTagTask($entity), 20);
								$entity->setArcherTagged(true);
							}
						}
					}
				}
			}
		}
	}
}