<?php

namespace vale\hcf\sage\models\entitys;

use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\block\Slab;
use pocketmine\block\Stair;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use vale\hcf\sage\models\util\IEManager;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TF;
use vale\hcf\sage\Sage;
use pocketmine\Player;
use vale\hcf\sage\SagePlayer;
use pocketmine\{Server};

class EndermiteEntity extends Human
{

	const FIND_DISTANCE = 15;

	const LOSE_DISTANCE = 25;

	const ATTACK_DISTANCE = 4;

	/** @var int */
	public $attackDamage;

	/** @var float */
	public $speed;

	/** @var int */
	public $attackWait;

	/** @var int */
	public $regenerationWait = 0;

	/** @var int */
	public $regenerationRate;

	/** @var int[] */
	protected $damages = [];

	/** @var SagePlayer|null */
	private $target = null;

	/** @var int */
	private $findNewTargetTicks = 0;

	/** @var int */
	private $jumpTicks = 5;

	/**
	 * BaseEntity constructor.
	 *
	 * @param Level $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt)
	{
		$manager = new IEManager(Sage::getInstance(), "endermiteskin.png");
		$this->setSkin($manager->skin);
		parent::__construct($level, $nbt);
		$this->setMaxHealth(50);
		$this->setNameTagAlwaysVisible(true);
		$this->setHealth(75);
		$this->setScale(0.50);
			$this->width = 1;
		$this->height = 1.8;
		$hp = $this->getHealth();
		$this->setNameTag("§5§lDefender");
		$this->attackDamage = 0;
		$this->regenerationRate = 0.1;
		$this->speed = 1;
		$this->setAttributes();
	}

	/**
	 * @return string
	 */
	public function getSaveId(): string
	{
		return self::class;
	}

	public function entityBaseTick(int $tickDiff = 1): bool
	{
		parent::entityBaseTick($tickDiff);
		if (!$this->isAlive()) {
			if (!$this->closed) {
				$this->flagForDespawn();
			}
			return false;
		}
		$this->setNameTag($this->getNameTag());
		if ($this->regenerationWait-- <= 0) {
			$this->setHealth($this->getHealth() + $this->regenerationRate);
			$this->regenerationWait = 20;
		}
		$health = round($this->getHealth());
		$maxHealth = $this->getMaxHealth();
		$times = (int)round(($health / $maxHealth) * 20);
		$this->setScoreTag("§f$health HP");
		if ($this->hasTarget()) {
			return $this->attackTarget();
		}
		if ($this->findNewTargetTicks > 0) {
			$this->findNewTargetTicks--;
		}
		if (!$this->hasTarget() and $this->findNewTargetTicks === 0) {
			$this->findNewTarget();
		}
		if ($this->jumpTicks > 0) {
			$this->jumpTicks--;
		}
		if (!$this->isOnGround()) {
			if ($this->motion->y > -$this->gravity * 4) {
				$this->motion->y = -$this->gravity * 4;
			} else {
				$this->motion->y += $this->isUnderwater() ? $this->gravity : -$this->gravity;
			}
		} else {
			$this->motion->y -= $this->gravity;
		}
		$this->move($this->motion->x, $this->motion->y, $this->motion->z);
		if ($this->shouldJump()) {
			$this->jump();
		}
		$this->updateMovement();
		return $this->isAlive();
	}

	/**
	 * @return bool
	 */
	public function attackTarget(): bool
	{
		$target = $this->getTarget();
		if ($target == null or $target->distance($this) >= self::LOSE_DISTANCE or $this->target->isCreative() or $this->target->isClosed()) {
			$this->target = null;
			return true;
		}
		if ($this->jumpTicks > 0) {
			$this->jumpTicks--;
		}
		if (!$this->isOnGround()) {
			if ($this->motion->y > -$this->gravity * 4) {
				$this->motion->y = -$this->gravity * 4;
			} else {
				$this->motion->y += $this->isUnderwater() ? $this->gravity : -$this->gravity;
			}
		} else {
			$this->motion->y -= $this->gravity;
		}
		$this->move($this->motion->x, $this->motion->y, $this->motion->z);
		if ($this->shouldJump()) {
			$this->jump();
		}
		$x = $target->x - $this->x;
		$y = $target->y - $this->y;
		$z = $target->z - $this->z;
		if ($x * $x + $z * $z < 1.2) {
			$this->motion->x = 0;
			$this->motion->z = 0;
		} else {
			$this->motion->x = $this->getSpeed() * 0.15 * ($x / (abs($x) + abs($z)));
			$this->motion->z = $this->getSpeed() * 0.15 * ($z / (abs($x) + abs($z)));
		}
		$this->yaw = rad2deg(atan2(-$x, $z));
		$this->pitch = rad2deg(-atan2($y, sqrt($x * $x + $z * $z)));
		$this->move($this->motion->x, $this->motion->y, $this->motion->z);
		if ($this->shouldJump()) {
			$this->jump();
		}
		if ($this->distance($target) <= self::ATTACK_DISTANCE and $this->attackWait <= 0) {
			$damage = mt_rand(1, 9);
			if ($target->getHealth() <= $damage) {
				$this->target = null;
				$this->findNewTarget();
			}
			#$target->setHealth($target->getHealth() - $damage);
			$deltaX = $target->x - $this->x;
			$deltaZ = $target->z - $this->z;
			$target->knockBack($this, $this->getBaseAttackDamage(), $deltaX, $deltaZ);
			$target->doHitAnimation();
			$this->broadcastEntityEvent(ActorEventPacket::ARM_SWING);
			$this->attackWait = 17;
		}
		$this->updateMovement();
		$this->attackWait--;
		return $this->isAlive();
	}

	public function attackspecifictARg(SagePlayer $SagePlayer){
		$this->target = $SagePlayer;
	}

	/**
	 * @param EntityDamageEvent $source
	 */
	public function attack(EntityDamageEvent $source): void{
		$this->setHealth($this->getHealth() - 1);

	}

	/**
	 * @param Entity $attacker
	 * @param float $damage
	 * @param float $x
	 * @param float $z
	 * @param float $base
	 */
	public function knockBack(Entity $attacker, float $damage, float $x, float $z, float $base = 0.4): void
	{
		parent::knockBack($attacker, $damage, $x, $z, $base * 2);
	}

	public function findNewTarget()
	{
		$distance = self::FIND_DISTANCE;
		$target = null;
		foreach ($this->getLevel()->getPlayers() as $SagePlayer) {
			if ($SagePlayer instanceof self) {
				continue;
			}
			if ($SagePlayer instanceof SagePlayer and $SagePlayer->distance($this) <= $distance and (!$SagePlayer->isCreative())) {
				$distance = $SagePlayer->distance($this);
				$target = $SagePlayer;
			}
		}
		$this->findNewTargetTicks = 60;
		$this->target = ($target != null ? $target : null);
	}

	/**
	 * @return bool
	 */
	public function hasTarget(): bool
	{
		$target = $this->getTarget();
		if ($target == null) {
			return false;
		}
		return true;
	}

	/**
	 * @return SagePlayer|null
	 */
	public function getTarget(): ?SagePlayer
	{
		return $this->target;
	}

	/**
	 * @return float
	 */
	public function getSpeed(): float
	{
		return ($this->isUnderwater() ? $this->speed / 2 : $this->speed);
	}

	/**
	 * @return int
	 */
	public function getBaseAttackDamage(): int
	{
		return $this->attackDamage;
	}

	/**
	 * @param int $y
	 *
	 * @return Block
	 */
	public function getFrontBlock($y = 0): Block
	{
		$dv = $this->getDirectionVector();
		$pos = $this->asVector3()->add($dv->x * $this->getScale(), $y + 1, $dv->z * $this->getScale())->round();
		return $this->getLevel()->getBlock($pos);
	}

	/**
	 * @return bool
	 */
	public function shouldJump(): bool
	{
		if ($this->jumpTicks > 0) {
			return false;
		}
		return $this->isCollidedHorizontally or
			($this->getFrontBlock()->getId() != 0 or $this->getFrontBlock(-1) instanceof Stair) or
			($this->getLevel()->getBlock($this->asVector3()->add(0, -0, 5)) instanceof Slab and
				(!$this->getFrontBlock(-0.5) instanceof Slab and $this->getFrontBlock(-0.5)->getId() != 0)) and
			$this->getFrontBlock(1)->getId() == 0 and
			$this->getFrontBlock(2)->getId() == 0 and
			!$this->getFrontBlock() instanceof Flowable and
			$this->jumpTicks == 0;
	}

	/**
	 * @return int
	 */
	public function getJumpMultiplier(): int
	{
		if ($this->getFrontBlock() instanceof Slab or $this->getFrontBlock() instanceof Stair or
			$this->getLevel()->getBlock($this->asVector3()->subtract(0, 0.5)->round()) instanceof Slab and
			$this->getFrontBlock()->getId() != 0) {
			$fb = $this->getFrontBlock();
			if ($fb instanceof Slab and $fb->getDamage() & 0x08 > 0) {
				return 8;
			}
			if ($fb instanceof Stair and $fb->getDamage() & 0x04 > 0) {
				return 8;
			}
			return 4;
		}
		return 16;
	}

	public function jump(): void
	{
		$this->motion->y = $this->gravity * $this->getJumpMultiplier();
		$this->move($this->motion->x * 1.25, $this->motion->y, $this->motion->z * 1.25);
		$this->jumpTicks = 5;
	}

	/**
	 * @return array
	 */
	public function getDrops(): array
	{
		return [];
	}

	public function onDeath(): void
	{

		#   $this->getLevel()->dropItem($this, $item);
	}

	public function setAttributes() {
		$sword = Item::get(Item::IRON_INGOT, 0, 1);
		$this->getInventory()->setItemInHand($sword);
		$this->getArmorInventory()->setHelmet(Item::get(Item::GOLD_HELMET, 0, 1));
		$this->getArmorInventory()->setChestplate(Item::get(Item::GOLD_CHESTPLATE, 0, 1));
		$this->getArmorInventory()->setLeggings(Item::get(Item::GOLD_LEGGINGS, 0, 1));
		$this->getArmorInventory()->setHelmet(Item::get(Item::GOLD_BOOTS, 0, 1));

	}
}