<?php

declare(strict_types = 1);

#   /\ /\___ _ __ __ _ ___
#   \ \ / / _ \ '__/ _`|/_\
#    \ V / __/ | | (_| |__/
#     \_/ \___ |_| \__,|\___
#                  |___/

namespace vale\hcf\sage\system\monthlycrates\util;

//Base LIbraries
use pocketmine\{
    Player,
    Server,
    level\Level,
    entity\Entity,
};
//Packets
use pocketmine\network\mcpe\protocol\{AddActorPacket, PlaySoundPacket};
use vale\hcf\sage\SagePlayer;

class CrateSounds{

    public const NOTE_PLING = "note.pling";
    public const WITHER_BREAK_BLOCK = "mob.wither.break_block"; //reward result
    public const NOTE_BD = "note.bd"; //play open opening ah
    public const NOTE_HARP = "note.harp"; //Crate roll
    public const RANDOM_TOAST = "random.toast"; //the sound for xbox invite
    public const HORSE_ARMOR = "mob.horse.armor";
    public const VILLAGER_HAGGLE = "mob.villager.haggle";
    public const LEVEL_UP = "random.levelup";
    #public const NOTE_PLING = "note.pling";


   public static function playSound(Entity $player, string $sound, $volume = 1, $pitch = 1, int $radius = 5): void
	{
		if ($player instanceof SagePlayer) {
			if ($player === null) {
				return;
			}
			if (!$player->isOnline()) {
				return;
			}
			

			if ($player->isOnline()) {
				$spk = new PlaySoundPacket();
				$spk->soundName = $sound;
				$spk->x = $player->getX();
				$spk->y = $player->getY();
				$spk->z = $player->getZ();
				$spk->volume = $volume;
				$spk->pitch = $pitch;
				$player->dataPacket($spk);
			}
		}
	}
}
