<?php

declare(strict_types = 1);

namespace vale\hcf\sage\system\cyberattack;

//Player & Server
use pocketmine\{Server, Player};
//Entity Library
use pocketmine\entity\{
	Entity
};
//block
use pocketmine\block\Block;
//lvl
use pocketmine\level\{Level, Position, particle\HugeExplodeSeedParticle};
//Event
use pocketmine\event\entity\{EntityDamageByEntityEvent, EntityDamageEvent};
#use pocketmine\level\{Level};
//NBT
use pocketmine\nbt\tag\{CompoundTag, IntTag, ListTag, StringTag};
//Other api's
use vale\hcf\sage\Sage;
//Item libs
use pocketmine\item\{Item, enchantment\Enchantment, enchantment\EnchantmentInstance};
//CustomEnchants
//packets
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use vale\hcf\sage\SagePlayer;

class CyberEntity extends Entity
{

	/** @var int * */
	public const  NETWORK_ID = self::ENDER_CRYSTAL;

	/** @var float * */
	public $height = 0.98;
	/** @var float * */
	public $width = 0.98;

	/** @var string * */
	public $nametag = "§r§l§d** METEOR **\n§r§7§o(TIP: Tap this meteor 200x to unlock it's legendary treasures)";
	/** @var int * */
	public $maxhealth = 1;

	/** @var int * */
	public const TAPS_TO_UNLOCK = 200;
	/** @var string * */
	public const TAP_DATA = "taps2unlock";

	/** @var int * */
	public $despawnTime = 60 * 15 * 20; //15 mins


	public function getName(): string
	{
		return "envoy";
	}

	public function entityBaseTick(int $tick = 1): bool
	{
		$this->despawnTime--;
		if ($this->despawnTime <= 0 && !$this->isFlaggedForDespawn()) {
			$this->flagForDespawn();
		}

		return parent::entityBaseTick($tick);
	}

	public function initEntity(): void
	{
		$this->setMaxHealth(1);
		$this->setHealth(1);
		$this->setScale(3.2);
		$this->setNameTag($this->nametag);
		$this->setNameTagAlwaysVisible(true);
		$this->setCanSaveWithChunk(false);
		$this->namedtag->setInt("taps2unlock", self::TAPS_TO_UNLOCK);

	}

	/**
	 * @param EntityDamageEvent $event
	 */

	public function attack(EntityDamageEvent $event): void
	{
		if ($event instanceof EntityDamageByEntityEvent) {
			$entity = $event->getEntity();
			$damager = $event->getDamager();
			if ($damager instanceof SagePlayer) {
				$event->setCancelled();
				$tapsleft = $entity->namedtag->getInt(self::TAP_DATA);
				if ($tapsleft > 0) {
					$tapsleft--;
					$entity->namedtag->setInt(self::TAP_DATA, $tapsleft);
				}
				$entity->setNameTag("§r§l§d** CRYSTAL **\n§r§7§o(TIP: Tap this crystal " . $tapsleft . "x to unlock it's legendary treasures)");
				if ($tapsleft == self::TAPS_TO_UNLOCK / 2) {
					$demon1 = new CyberDefender($entity->getLevel(), self::createBaseNBT(new Position($entity->x + mt_rand(0, 4), $entity->y, $entity->z + mt_rand(0, 4), $entity->getLevel())));
					$demon1->spawnToAll();
					$demon2 = new CyberDefender($entity->getLevel(), self::createBaseNBT(new Position($entity->x + mt_rand(0, 4), $entity->y, $entity->z + mt_rand(0, 4), $entity->getLevel())));
					$demon2->spawnToAll();
					$guardian = new CyberDefender($entity->getLevel(), self::createBaseNBT(($pos = new Position($entity->x + mt_rand(0, 4), $entity->y + 1, $entity->z + mt_rand(0, 4), $entity->getLevel()))));
					$guardian->spawnToAll();

					Sage::playSound($damager, "mob.wither.spawn", 500, 1, 13);
				}
				if ($tapsleft == 0) {
					$entity->getLevel()->addParticle(new HugeExplodeSeedParticle($entity->asVector3()));
					$entity->getLevel()->broadcastLevelSoundEvent($damager->asVector3(), LevelSoundEventPacket::SOUND_EXPLODE);
					$entity->getLevel()->dropItem($entity->asVector3()->add(0, 1, 0), self::getReward());
					$entity->getLevel()->dropItem($entity->asVector3()->add(0, 1, 0), self::getReward());
				    Server::getInstance()->broadcastMessage("§r§6{$damager->getName()} §r§7has claimed a §r§6§lCrystal!");
					$entity->flagForDespawn();
				}
			}
		}
	}

	public  function getMaxHP(): int
	{
		return $this->maxhealth;
	}


	public static function spawnEnvoy(Position $pos): self
	{
#$pos->getLevel()->loadChunk(round($pos->getX()), round($pos->getZ()), true);
		$entity = new self($pos->getLevel(), self::createBaseNBT($pos));
		$entity->spawnToAll();
		Sage::playSound($entity, "mob.wither.death", 500, 1, 13);
		return $entity;
	}


	public static function getReward()
	{
		$key4 = Item::get(Item::DYE, 9, rand(1,16));
				$key4->setCustomName("§r§d§lHaze §r§7Key (Right-Click)");
				$key4->setLore([
					"§r§7Right-Click this key on the §l§dHaze",
					"§r§l§dCrate §r§7located at §dspawn §7to obtain rewards.",
					"§r",
					"§r§dstore.buycraft.net"
				]);
				$key4->setNamedTagEntry(new ListTag("ench"));
				$key4->getNamedTag()->setTag(new StringTag("hazekeyxd"));
	
				
				$keya = Item::get(Item::TRIPWIRE_HOOK, 0, rand(1,16));
				$keya->setCustomName("§r§e§lAegis §r§7Key (Right-Click)");
				$keya->setLore([
					"§r§7Right-Click this key on the §l§eAegis",
					"§r§l§eCrate §r§7located at §espawn §7to obtain rewards.",
					"§r",
					"§r§estore.buycraft.net"
				]);
				$keya->setNamedTagEntry(new ListTag("ench"));
				$keya->getNamedTag()->setTag(new StringTag("aegiskeyxd"));
	
	
				$keyu = Item::get(Item::TRIPWIRE_HOOK, 0, rand(1,16));
				$keyu->setCustomName("§r§5§lSage §r§7Key (Right-Click)");
				$keyu->setLore([
					"§r§7Right-Click this key on the §l§5Spawn",
					"§r§l§5Crate §r§7located at §5spawn §7to obtain rewards.",
					"§r",
					"§r§5store.buycraft.net"
				]);
				$keyu->setNamedTagEntry(new ListTag("ench"));
				$keyu->getNamedTag()->setTag(new StringTag("sagekeyxd"));
		
	
				$summerob = Item::get(Item::ENDER_EYE, 2 , rand(1,16));
				$summerob->setCustomName("§r§5§kkeeueueu§r§d§lSummer Orb 2.0§r§5§kkeeueueu")->
				setLore([
					'§r§7Tap this §r§d§lsacred §r§7item at the Orb Extractor',
					'§r§7To recieve rewards such as',
					'§r§d(§r§7Airdrops§r§d, §r§7Keys§r§d, §r§7Portable Kits§r§d',
					'§r§7Ranks§r§d, §r§7and More!§r§d)',
					'',
					'§r§d§lstore.hcf.net'
				]);
				$summerob->getNamedTag()->setTag(new StringTag("summerkeyxd"));
			

				$key = Item::get(Item::TRIPWIRE_HOOK, 0, rand(1,16));
				$key->setCustomName("§r§c§lAbility §r§7Key (Right-Click)");
				$key->setLore([
					"§r§7Right-Click this key on the §l§cAbility",
					"§r§l§cCrate §r§7located at §cspawn §7to obtain rewards.",
					"§r",
					"§r§cstore.buycraft.net"
				]);
				$key->setNamedTagEntry(new ListTag("ench"));
				$key->getNamedTag()->setTag(new StringTag("abilitykeyxd"));
	

		$items = [$key, $summerob, $keyu, $keya, $key4];

		$item = $items[array_rand($items)];

		return $item;


	}

}
