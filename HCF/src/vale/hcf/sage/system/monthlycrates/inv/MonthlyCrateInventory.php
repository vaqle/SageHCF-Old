<?php

declare(strict_types = 1);

#   /\ /\___ _ __ __ _ ___
#   \ \ / / _ \ '__/ _`|/_\
#    \ V / __/ | | (_| |__/
#     \_/ \___ |_| \__,|\___
#                  |___/

namespace vale\hcf\sage\system\monthlycrates\inv;

//Base Libraries
use pocketmine\{
	Server, Player,
};
use vale\hcf\sage\SagePlayer;
use vale\hcf\sage\system\monthlycrates\MonthlyCrates;
use vale\hcf\sage\system\monthlycrates\util\CrateSounds;
use vale\hcf\sage\system\monthlycrates\util\CrateUtils;

use pocketmine\level\{Level, sound\AnvilFallSound, Position, particle\FloatingTextParticle};
//Block & Item
use pocketmine\{
	block\Block, item\Item
};
//nbts
use pocketmine\nbt\tag\{CompoundTag, IntTag, StringTag, ListTag};
use pocketmine\math\Vector3;
//Event Libraries
use pocketmine\event\Listener;
use pocketmine\inventory\transaction\action\SlotChangeAction;
//InvMenu
use muqsit\invmenu\InvMenu;
//Core
use vale\hcf\sage\Sage;
//Tasks
use vale\hcf\sage\system\monthlycrates\tasks\SlotGridShuffleTask;
use vale\hcf\sage\system\monthlycrates\tasks\FinalAnimationTask;


class MonthlyCrateInventory
{


	public static function open(SagePlayer $player, string $type): void{
		/** @var $menu **/
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);

		$menu->setName(CrateUtils::getCrateTypeName($type));
		CrateUtils::setInventoryItems($menu->getInventory(), $type);


		$menu->setListener(function(Player $player, Item $itemClicked, Item $itemClickedWith, SlotChangeAction $action) {
			if(in_array($player->getName(), MonthlyCrates::$opening)){
				switch(MonthlyCrates::$opening[$player->getName()]) {
					case "july2019":
						/**if($itemClicked->getId() !== 0){
						CrateSounds::playSound($player, CrateSounds::HORSE_ARMOR);
						Core::getInstance()->getScheduler()->scheduleRepeatingTask(new SlotGridShuffleTask($player, $action->getInventory(), "july2019", $action->getSlot(), 12), 10);
						}**/
						if (in_array($action->getSlot(), CrateUtils::$grid) && $itemClicked->getId() == 130 && $itemClicked->getDamage() == 5 && $itemClicked->hasCustomBlockData()) {
							CrateSounds::playSound($player, CrateSounds::HORSE_ARMOR);
							Sage::getInstance()->getScheduler()->scheduleRepeatingTask(new SlotGridShuffleTask($player, $action->getInventory(), "july2019", $action->getSlot(), 25), 5);
						}
						if ($itemClicked->getId() == 130 && $itemClicked->getDamage() == 1) {
							if((!$action->getInventory()->getItem(12)->hasCustomBlockData() && !$action->getInventory()->getItem(13)->hasCustomBlockData() && !$action->getInventory()->getItem(14)->hasCustomBlockData() && !$action->getInventory()->getItem(21)->hasCustomBlockData() && !$action->getInventory()->getItem(22)->hasCustomBlockData() && !$action->getInventory()->getItem(23)->hasCustomBlockData() && !$action->getInventory()->getItem(30)->hasCustomBlockData() && !$action->getInventory()->getItem(31)->hasCustomBlockData() && !$action->getInventory()->getItem(32)->hasCustomBlockData())){
								CrateSounds::playSound($player, CrateSounds::HORSE_ARMOR);
								$reward = CrateUtils::getBonusReward("july2019");
								$action->getInventory()->setItem($action->getSlot(), $reward);
								$player->getInventory()->addItem($reward);
								unset(MonthlyCrates::$opening[array_search($player->getName(), MonthlyCrates::$opening)]);
							}else{

								$player->sendMessage("§l§c(!) §r§cYou cannot reveal the final reward until all previous items have been looted!");
								$player->getLevel()->addSound(new AnvilFallSound($player));
							}
							#$player->removeWindow($action->getInventory());
							#Core::getInstance()->scheduleRepeatingTask(new SlotGridShuffleTask($player, $action->getInventory(), $type, $action->getSlot(), 10), 10);
						}
				}
			}
		});
		$menu->readonly(true);
		$menu->send($player);
	}

	public static function initTask(Player $player, string $type, SlotChangeAction $action): void{
		Sage::getInstance()->getScheduler()->scheduleRepeatingTask(new SlotGridShuffleTask($player, $action->getInventory(), $type, $action->getSlot(), 15), 0);
	}
}