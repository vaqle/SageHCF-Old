<?php


namespace vale\hcf\sage\partneritems\tasks;


use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use vale\hcf\sage\models\entitys\EndermiteEntity;
use vale\hcf\sage\SagePlayer;

class GuardianAttackTask extends Task
{
	/**
	 * @var EndermiteEntity
	 */
	private  $guard;
	/**
	 * @var Entity|Player
	 */
	private $entity;
	/**
	 * @var Entity|Player
	 */
	private $damager;


	public $seconds = 30;
	/**
	 * @var EntityDamageByEntityEvent
	 */
	private $ev;

	/**
	 * GuardianAttackTask constructor.
	 * @param EndermiteEntity $gaurd
	 * @param Entity|Player $entity
	 * @param EntityDamageByEntityEvent $ev
	 * @param Entity|Player $damager
	 */
	public function __construct(EndermiteEntity $gaurd, SagePlayer $entity, EntityDamageEvent $ev, SagePlayer $damager)
	{
		$this->guard = $gaurd;
		$this->entity = $entity;
		$this->ev = $ev;
		$this->damager = $damager;
	}

	public function onRun(int $currentTick)
	{
		--$this->seconds;
		if ($this->seconds === 29) {
			$guard = $this->guard;
			$entity = $this->entity;
			$damager = $this->damager;
			$ev = $this->ev;
			if ($guard->getTarget() != $damager->getName()) {
				if ($guard->getTarget() === $entity->getName()) {
					if ($ev instanceof EntityDamageByEntityEvent) {
						$damager = $ev->getDamager();
						$guard->getTarget()->getName() === $entity->getNameTag();
						$target = $guard->getTarget();
						$target = $damager->getNameTag();
						$guard->lookAt($entity);
						$guard->attackspecifictARg($entity);
						$gaurd1 = new EndermiteEntity($entity->getLevel(), Entity::createBaseNBT($entity->asVector3()));
						$gaurd1->spawnToAll();
						$gaurd1->attackspecifictARg($entity);
						$gaurd1->lookAt($entity);
						$gaurd1->attackspecifictARg($entity);
						$entity->addEffect(new EffectInstance(Effect::getEffect(9), 8*20, 4));
					}
				}
			}
		}

		if ($this->seconds === 28) {
			$guard = $this->guard;
			$entity = $this->entity;
			$damager = $this->damager;
			$ev = $this->ev;
			if ($guard->getTarget() != $damager->getName()) {
				if ($guard->getTarget() === $entity->getName()) {
					if ($ev instanceof EntityDamageByEntityEvent) {
						$damager = $ev->getDamager();
						$guard->getTarget()->getName() === $entity->getNameTag();
						$target = $guard->getTarget();
						$target = $damager->getNameTag();
						$guard->lookAt($entity);
						$gaurd1 = new EndermiteEntity($entity->getLevel(), Entity::createBaseNBT($entity->asVector3()));
						$gaurd1->spawnToAll();
						$gaurd1->attackspecifictARg($entity);
						$gaurd1->lookAt($entity);
						$entity->addEffect(new EffectInstance(Effect::getEffect(4), 8*20, 4));
						$guard->attackspecifictARg($entity);
					}
				}
			}
		}

		if ($this->seconds === 27) {
			$guard = $this->guard;
			$entity = $this->entity;
			$damager = $this->damager;
			$ev = $this->ev;
			if ($guard->getTarget() != $damager->getName()) {
				if ($guard->getTarget() === $entity->getName()) {
					if ($ev instanceof EntityDamageByEntityEvent) {
						$damager = $ev->getDamager();
						$guard->getTarget()->getName() === $entity->getNameTag();
						$target = $guard->getTarget();
						$target = $damager->getNameTag();
						$guard->lookAt($entity);
						$gaurd1 = new EndermiteEntity($entity->getLevel(), Entity::createBaseNBT($entity->asVector3()));
						$gaurd1->spawnToAll();
						$gaurd1->attackspecifictARg($entity);
						$gaurd1->lookAt($entity);
						$entity->addEffect(new EffectInstance(Effect::getEffect(19), 10*20, 4));
						$guard->attackspecifictARg($entity);
					}
				}
			}
		}

		if ($this->seconds === 4) {
			$entity = $this->entity;
			$guard = $this->guard;
			$damager =  $this->damager;
			if ($guard->isAlive()) {
				$guard->kill();
				$entity->addEffect(new EffectInstance(Effect::getEffect(4), 8*20, 4));
				$entity->addEffect(new EffectInstance(Effect::getEffect(4), 8*20, 4));
				self::playSound($damager, "mob.enderdragon.growl", 0.7, 0.8);
			}

		}
	}
	public static function playSound(Entity $player, string $sound, $volume = 1, $pitch = 1, int $radius = 5): void
	{
		foreach ($player->getLevel()->getNearbyEntities($player->getBoundingBox()->expandedCopy($radius, $radius, $radius)) as $p) {
			if ($p instanceof SagePlayer) {
				$spk = new PlaySoundPacket();
				$spk->soundName = $sound;
				$spk->x = $p->getX();
				$spk->y = $p->getY();
				$spk->z = $p->getZ();
				$spk->volume = $volume;
				$spk->pitch = $pitch;
				$p->dataPacket($spk);
			}
		}
	}
}