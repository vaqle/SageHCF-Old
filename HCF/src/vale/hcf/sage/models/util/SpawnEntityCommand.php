<?php


namespace vale\hcf\sage\models\util;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use vale\hcf\sage\models\entitys\BlockMerchant;
use vale\hcf\sage\models\entitys\CrystalBlackSmith;
use vale\hcf\sage\models\entitys\DeathbanEntity;
use vale\hcf\sage\models\entitys\PartnerPackageEntity;
use vale\hcf\sage\models\entitys\PortableBambeEntity;
use vale\hcf\sage\models\entitys\PotionSpawnerEntity;
use vale\hcf\sage\models\entitys\TopEntity;
use vale\hcf\sage\models\entitys\TopKillsEntity;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class SpawnEntityCommand extends PluginCommand{

	public function __construct(string $name, Sage $owner)
	{
		parent::__construct($name, $owner);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if ($sender instanceof SagePlayer) {
			if (!isset($args[0])) {
				$sender->sendMessage("/spawnentity <name>");
				return false;
			}

			if(!$sender->hasPermission("core.spe")){
				return false;
			}
			switch ($args[0]) {
				case "crystal":
					$bot = new CrystalBlackSmith($sender->getLevel(), Entity::createBaseNBT($sender->asVector3()));
					$bot->spawnToAll();
					break;

				case "kills":
					$bot = new TopKillsEntity($sender->getLevel(), Entity::createBaseNBT($sender->asVector3()));
					$bot->spawnToAll();
					break;
				case "top":
					$bot = new TopEntity($sender->getLevel(), Entity::createBaseNBT($sender->asVector3()));
					$bot->spawnToAll();
					break;
				case "partner":
					$gaurd = new PartnerPackageEntity($sender->getLevel(), Entity::createBaseNBT($sender->asVector3()));
					$gaurd->spawnToAll();
					break;
				case "bambe":
					    $gaurd = new PortableBambeEntity($sender->getLevel(), Entity::createBaseNBT($sender->asVector3()));
						$gaurd->spawnToAll();
					    break;
				case "merchant":
					$bot = new BlockMerchant($sender->getLevel(), Entity::createBaseNBT($sender->asVector3()));
					$bot->spawnToAll();
					break;

				case "potion":
					$bot = new PotionSpawnerEntity($sender->getLevel(), Entity::createBaseNBT($sender->asVector3()));
					$bot->spawnToAll();
					break;

				case "deathban":
					$bot = new DeathbanEntity($sender->getLevel(), Entity::createBaseNBT($sender->asVector3()));
					$bot->spawnToAll();
					break;
					
				case "clearpot":
					foreach ($sender->getLevel()->getEntities() as $entity){
						if($entity instanceof PotionSpawnerEntity){
							$entity->kill();
						}
					}
					break;

				case "clearmerchant":
					foreach ($sender->getLevel()->getEntities() as $entity){
						if($entity instanceof BlockMerchant){
							$entity->kill();
						}
					}
					break;

				case "clearcrystal":
					foreach ($sender->getLevel()->getEntities() as $entity){
						if($entity instanceof CrystalBlackSmith){
							$entity->kill();
						}
					}
					break;

			}
		}
		return true;
	}

}
