<?php

namespace vale\hcf\sage\factions;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\MenuIds;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use vale\hcf\sage\Sage;
use vale\hcf\sage\SagePlayer;

class FactionsPermissionManager
{
	public $member;

	public function sendFactionsManagementMenu(SagePlayer $player)
	{
		$menu = InvMenu::create(MenuIds::TYPE_CHEST);
		$menu->readonly(true);
		$faction = $player->getFaction();
		$i = 0;
		$menu->setName("§r§7Factions Management");
		$sethome = Item::get(Item::YELLOW_GLAZED_TERRACOTTA);
		$sethome->setCustomName("§r§e§lSethome");
		$sethome->setLore([
			'§r§7Tap this to §r§eset §r§7the faction home your faction',
		]);
		$menu->getInventory()->setItem(24, $sethome);
		foreach (Sage::getFactionsManager()->getMembers($faction) as $member) {
			$skull = Item::get(397, 3);
			$menu->getInventory()->setItem($i++, $skull->setCustomName($member)->setLore(["§r§7Faction: {$faction} \n §r§7Name: {$member}"]));
		}
		if ($player->isInFaction() && Sage::getFactionsManager()->getLeader($faction) === $player->getName()) {
			$menu->send($player);
			$menu->setListener(function (SagePlayer $player, Item $itemClicked, Item $itemClickedWith, SlotChangeAction $action) {
				$faction = $player->getFaction();
				foreach (Sage::getFactionsManager()->getMembers($faction) as $memberent) {
					if ($itemClicked->getCustomName() === $memberent) {
						$this->member = $memberent;
						$this->editFactionMembersPerm($player);

					}
				}
				if($itemClicked->getCustomName() === "§r§e§lSethome"){
					if ($player->isInFaction()) {
						if (Sage::getFactionsManager()->getLeader($player->getFaction()) == $player->getName() || Sage::getFactionsManager()->isOfficer($faction, $player)) {
							if (!$player->isPvp()) {
								if ($player->isInClaim()) {
									if (Sage::getFactionsManager()->getClaimer($player->x, $player->z) == $player->getFaction()) {
										Sage::getFactionsManager()->setHome($player->getFaction(), $player->getPosition());
										$player->sendMessage("§r§7You have succesfully set the FHOME");


									}else{
										$player->sendMessage("Make sure you are not in pvp timer. your in a faction and your far enough out");
									}
								}
							}
						}
					}
				}
			});
		}
	}

	public function editFactionMembersPerm(SagePlayer $player)
	{
		$menu = InvMenu::create(MenuIds::TYPE_CHEST);
		$kick = Item::get(Item::RED_GLAZED_TERRACOTTA);
		$menu->setName("§r§6§lEditing §r§7 " . $this->member . "s " . "§r§6Permissions" );
		$kick->setCustomName("§r§c§lKick");
		$kick->setLore([
			'§r§7Tap this to §r§c§lremove §r§7the specified player from your faction',
			'§r§7This will §r§c§lkick §r§7the player, you may not do this during combat',
		]);
		$promote = Item::get(Item::GREEN_GLAZED_TERRACOTTA);
		$promote->setCustomName("§r§a§lPromote");
		$promote->setLore([
			'§r§7Tap this to §r§apromote §r§7the specified player from your faction',
		]);
		$demote = Item::get(Item::BLUE_GLAZED_TERRACOTTA);
		$demote->setCustomName("§r§b§lDemote");
		$demote->setLore([
			'§r§7Tap this to §r§bdemote §r§7the specified player from your faction',
		]);
		$menu->getInventory()->setItem(0, $kick);
		$menu->getInventory()->setItem(1, $promote);
		$menu->getInventory()->setItem(2, $demote);
		$member = $this->member;
		$menu->readonly(true);
		$menu->send($player);
		$menu->setListener(function (SagePlayer $player, Item $itemClicked, Item $itemClickedWith, SlotChangeAction $action) {
			$faction = $player->getFaction();
			if($itemClicked->getCustomName() === "§r§c§lKick"){
				Sage::getFactionsManager()->kick($this->member);
				$player->sendMessage("You have §r§ckicked ". $this->member . " §r§7from your faction");
			}
			if($itemClicked->getCustomName() === "§r§a§lPromote"){
				Sage::getFactionsManager()->setOfficer($faction, $this->member);
				$player->sendMessage("§r§7You have succesfully promoted §r§a{$this->member}");
			}

			if($itemClicked->getCustomName() === "§r§b§lDemote"){
				if(Sage::getFactionsManager()->isOfficer($faction, $this->member)){
					Sage::getFactionsManager()->setMember($faction, $this->member);
					$player->sendMessage("§r§You have succesfully §r§b§ldemoted §r§7 ". $this->member);
				}

			}

		});
	}
}