<?php

namespace vale\hcf\sage\models\tiles;

use vale\hcf\sage\models\tiles\TileIDS;
use vale\hcf\sage\models\tiles\DispenserTile;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\block\Solid;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\Tile;

class DispenserBlock extends Solid{

    public function __construct(int $meta = 0){
        parent::__construct(BlockIds::DISPENSER, $meta);
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        $this->meta = $faces[$face] ?? $face;
        parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        Tile::createTile(TileIDS::DISPENSER, $this->getLevel(), DispenserTile::createNBT($this));
        return true;
    }

    public function onActivate(Item $item, Player $player = null): bool{
        if($player !== null){
            /** @var DispenserTile|null $tile */
            $tile = $this->getLevel()->getTile($this);

            if($tile === null){
                $tile = Tile::createTile(TileIDS::DISPENSER, $this->getLevel(), DispenserTile::createNBT($this));
            }
            if($tile instanceof DispenserTile){
                $player->addWindow($tile->getInventory());
            }
        }
        return true;
    }

    public function getHardness(): float{
        return 3.5;
    }
}