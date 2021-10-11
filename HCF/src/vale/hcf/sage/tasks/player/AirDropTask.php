<?php

declare(strict_types = 1);


namespace vale\hcf\sage\tasks\player;

use pocketmine\scheduler\Task;
use pocketmine\{Server, Player};
use pocketmine\math\Vector3;
use pocketmine\entity\{Entity, Effect, EffectInstance};
use pocketmine\block\Block;
use pocketmine\item\{enchantment\Enchantment, enchantment\EnchantmentInstance, Item};
use pocketmine\inventory\Inventory;
use pocketmine\level\{Location, Level, Position};
use pocketmine\level\sound\GhastSound;
use pocketmine\level\particle\{DestroyBlockParticle, FlameParticle, HugeExplodeSeedParticle};
use pocketmine\tile\Tile;
use pocketmine\nbt\tag\{CompoundTag, StringTag, IntTag, ListTag};
use pocketmine\network\mcpe\protocol\{AddActorPacket, PlaySoundPacket, LevelSoundEventPacket};
use vale\hcf\sage\models\tiles\DispenserTile;
use vale\hcf\sage\partneritems\PItemManager;
use vale\hcf\sage\Sage;

class AirDropTask extends Task
{


	private Player $player;
	private Level $level;
	private Vector3 $pos;
	private int $duration;
	private string $type;

	public function __construct(Player $player, Level $level, Vector3 $pos, int $duration, string $type)
	{
		$this->player = $player;
		$this->level = $level;
		$this->pos = $pos;
		$this->duration = $duration;
		$this->type = $type;
	}

	public function onRun($tick)
	{
		$this->duration--;

		if ($this->player === null or $this->player->isClosed()) {
			Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
			return;
		}

		switch ($this->type) {
			case "Elite":
			case "elite":
				$this->level->addParticle(new DestroyBlockParticle($this->pos, Block::get(236, 3)));
				if ($this->duration <= 0) {
					$nbt = new CompoundTag(" ", [
						new ListTag("Items", []),
						new StringTag("id", Tile::CHEST),
						new StringTag("CustomName", "AirDrop"),
						new IntTag("x", $this->pos->x),
						new IntTag("y", $this->pos->y - 1),
						new IntTag("z", $this->pos->z)
					]);
					$tile = DispenserTile::createTile("Chest", $this->level, $nbt);
					$this->level->setBlock($this->pos->subtract(0, 1, 0), Block::get(54));
					$ratio = 1;
					for ($y = 0; $y < 10; $y += 0.2) {
						$x = $ratio * cos($y);
						$z = $ratio * sin($y);
						$this->level->addParticle(new FlameParticle($this->pos->add($x, $y, $z)));
					}
					$this->level->addParticle(new HugeExplodeSeedParticle($this->pos));
					$this->level->addTile($tile);
					self::StrikeThunder($this->player, $this->level, $this->pos);
					$inv = $tile->getInventory();
					$tile->getInventory()->setItem(0, PItemManager::itemRand("rewards"));
					$tile->getInventory()->setItem(1, PItemManager::itemRand("rewards"));
					$tile->getInventory()->setItem(2, PItemManager::itemRand("rewards"));
					$tile->getInventory()->setItem(3, PItemManager::itemRand("rewards"));
					$tile->getInventory()->setItem(4, PItemManager::itemRand("rewards"));
					$tile->getInventory()->setItem(5, PItemManager::itemRand("rewards"));
					$tile->getInventory()->setItem(6, PItemManager::itemRand("rewards"));
					$tile->getInventory()->setItem(7, PItemManager::itemRand("rewards"));
					$tile->getInventory()->setItem(8, PItemManager::itemRand("rewards"));
					$tile->getInventory()->setItem(9, PItemManager::itemRand("rewards"));
					$tile->getInventory()->setItem(10, PItemManager::itemRand("rewards"));
					Sage::getInstance()->getScheduler()->cancelTask($this->getTaskId());
				}
				break;
		}
	}

	public static function StrikeThunder(Player $player, Level $level, Vector3 $pos): void
	{

		$level = $level;
		$light = new AddActorPacket();
		$light->type = "minecraft:lightning_bolt";
		$light->entityRuntimeId = Entity::$entityCount++;
		$light->position = $pos;
		$level->broadcastPacketToViewers($player, $light);
		$player->getLevel()->addSound(new GhastSound($player));
	}


	public static function setChestKitItems(Inventory $inv, string $id): void
	{

		switch (strtolower($id)) {
			case "astaroth":
				$h = PItemManager::itemRand("rewards");
				$c = PItemManager::itemRand("rewards");
				$l = PItemManager::itemRand("rewards");
				$b = PItemManager::itemRand("rewards");
				$sword = PItemManager::itemRand("rewards");
				$pick = PItemManager::itemRand("rewards");
				$axe = PItemManager::itemRand("rewards");
				$gaps = PItemManager::itemRand("rewards");

				foreach ([$h, $c, $l, $b, $sword, $pick, $axe, $gaps] as $items) {
					$inv->addItem($items);
				}


				break;
		}
	}
}
