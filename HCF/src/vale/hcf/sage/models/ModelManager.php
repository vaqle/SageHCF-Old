<?php

namespace vale\hcf\sage\models;

use libs\utils\fireworks\entity\FireworksRocket;
use libs\utils\fireworks\Fireworks;
use pocketmine\block\Block;
use vale\hcf\sage\models\entitys\EnderPearl;
use pocketmine\item\ItemFactory;
use vale\hcf\sage\items\item\BetterSnowballItem;
use vale\hcf\sage\models\blocks\FenceGate;
use vale\hcf\sage\models\blocks\Chest;
use vale\hcf\sage\models\entitys\BlockMerchant;
use vale\hcf\sage\models\entitys\DeathbanEntity;
use vale\hcf\sage\models\entitys\FakeLogger;
use vale\hcf\sage\models\entitys\Observer;
use vale\hcf\sage\models\entitys\PartnerPackageEntity;
use vale\hcf\sage\models\entitys\PotionSpawnerEntity;
use vale\hcf\sage\models\entitys\SwapBallEntity;
use vale\hcf\sage\models\entitys\TopEntity;
use vale\hcf\sage\models\entitys\TopKillsEntity;
use vale\hcf\sage\models\tiles\PotionSpawner;
use vale\hcf\sage\models\util\PartnerCrateInventory;
use vale\hcf\sage\system\cyberattack\CyberAttack;
use pocketmine\entity\Entity;
use vale\hcf\sage\models\entitys\CrystalBlackSmith;
use vale\hcf\sage\models\entitys\EndermiteEntity;
use vale\hcf\sage\models\entitys\PlayerLogger;
use vale\hcf\sage\models\entitys\TextEntity;
use vale\hcf\sage\models\entitys\PortableBambeEntity;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\tile\Tile;
use vale\hcf\sage\models\tiles\TileIDS;
use vale\hcf\sage\models\tiles\DispenserTile;
use vale\hcf\sage\models\tiles\DispenserBlock;
use vale\hcf\sage\models\util\EntityBase;
use vale\hcf\sage\Sage;
use vale\hcf\sage\system\cyberattack\CyberDefender;
use vale\hcf\sage\system\cyberattack\CyberEntity;

class ModelManager{

	public static function init(){
		#Entity::registerEntity(CyberDefender::class, true);
		Entity::registerEntity(EntityBase::class, true);
		#Entity::registerEntity(CyberEntity::class, true);
		#Entity::registerEntity(CyberAttack::class, true);
		Entity::registerEntity(CrystalBlackSmith::class, true);
		Entity::registerEntity(BlockMerchant::class, true);
		Entity::registerEntity(PortableBambeEntity::class, true);
		Entity::registerEntity(TextEntity::class, true);
		Entity::registerEntity(TopKillsEntity::class, true);
		Entity::registerEntity(DeathbanEntity::class, true);
		Entity::registerEntity(PlayerLogger::class, true);
		Entity::registerEntity(FireworksRocket::class, false, ["FireworksRocket"]);
		Entity::registerEntity(TopEntity::class, true);
	Entity::registerEntity(FakeLogger::class, true);
		Entity::registerEntity(PotionSpawnerEntity::class, true);
	#	Entity::registerEntity(Fireworks::class, true);
		Entity::registerEntity(EnderPearl::class, true, ["EnderPearl"]);
		Entity::registerEntity(EndermiteEntity::class, true);
		ItemFactory::registerItem(new \vale\hcf\sage\items\item\EnderPearl(), true);
		Entity::registerEntity(PartnerPackageEntity::class, true);
	#	Entity::registerEntity(SwapBallEntity::class, true);
		#Entity::registerEntity(B)
		#ItemFactory::registerItem(new BetterSnowballItem(), true);

		self::initTiles();
		Sage::getInstance()->getLogger()->info("REGISTERED ENTITES");
	}

  public static function initTiles(){
      Tile::registerTile(DispenserTile::class, [TileIDS::DISPENSER, "minecraft:dispenser"]);
      BlockFactory::registerBlock(new DispenserBlock(), true);

	  BlockFactory::registerBlock(new FenceGate(Block::OAK_FENCE_GATE, 0, "Oak Fence Gate"), true);
	  BlockFactory::registerBlock(new FenceGate(Block::SPRUCE_FENCE_GATE, 0, "Spruce Fence Gate"), true);
	  BlockFactory::registerBlock(new FenceGate(Block::BIRCH_FENCE_GATE, 0, "Birch Fence Gate"), true);
	  BlockFactory::registerBlock(new FenceGate(Block::JUNGLE_FENCE_GATE, 0, "Jungle Fence Gate"), true);
	  BlockFactory::registerBlock(new FenceGate(Block::DARK_OAK_FENCE_GATE, 0, "Dark Oak Fence Gate"), true);
	  BlockFactory::registerBlock(new FenceGate(Block::ACACIA_FENCE_GATE, 0, "Acacia Fence Gate"), true);
	  BlockFactory::registerBlock(new Chest(0), true);
	  Tile::registerTile(PotionSpawner::class);
	  BlockFactory::registerBlock(new Observer(), true);
	  Sage::getInstance()->getLogger()->info("REGISTERED Blocks/ TILES");

    
   } 
 }