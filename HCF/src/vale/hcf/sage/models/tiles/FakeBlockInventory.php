<?php

namespace vale\hcf\sage\models\tiles;

use vale\hcf\sage\Sage;
use pocketmine\Player;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\inventory\ContainerInventory;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
class FakeBlockInventory extends ContainerInventory{

    private $defaultSize;
    private $windowType;
    private $block;

    /** @var null|callable */
    private $packetCallable;

    /**
     * FakeBlockInventory constructor.
     * @param Vector3 $holder
     * @param int $size
     * @param Block|int $block
     * @param int $windowType
     * @param callable|null $packetCallable
     */
    public function __construct(Vector3 $holder, int $size = 27, $block = BlockIds::CHEST, int $windowType = WindowTypes::CONTAINER, callable $packetCallable = null){
        $holder->x = intval($holder->x);
        $holder->y = intval($holder->y);
        $holder->z = intval($holder->z);
        if(is_int($block)){
            $block = BlockFactory::get($block);
        }
        $this->block = $block;
        $this->defaultSize = $size;
        $this->windowType = $windowType;
        $this->packetCallable = $packetCallable;
        parent::__construct($holder, [], $size, null);
    }

    public function setTitle(string $title): void{
        $this->title = $this->name = $title;
    }

    public function getNetworkType(): int{
        return $this->windowType;
    }

    public function getDefaultSize(): int{
        return $this->defaultSize;
    }

    public function getName(): string{
        return "Inventory";
    }

    public function onOpen(Player $who): void{
        Sage::getInstance()->getSessionManager()->get($who)->setCurrentWindow($this);
        if($this->block->getId() !== BlockIds::AIR){
            $block = clone $this->block;
            $block->setComponents($this->holder->x, $this->holder->y, $this->holder->z);
            $who->getLevel()->sendBlocks([$who], [$block]);
        }
        parent::onOpen($who);
    }

    public function onClose(Player $who): void{
        Sage::getInstance()->getSessionManager()->get($who)->setCurrentWindow(null);
        if($this->block->getId() !== BlockIds::AIR){
            $who->getLevel()->sendBlocks([$who], [$who->getLevel()->getBlock($this->holder)]);
        }
        parent::onClose($who);
    }

    public function setPacketCallable(?callable $packetCallable): void{
        $this->packetCallable = $packetCallable;
    }

    /**
     * @param Player $player
     * @param DataPacket $packet
     * @return bool, false will cancel event, true will let event continue
     */
    public function handlePacket(Player $player, DataPacket $packet): bool{
        $callable = $this->packetCallable;

        if($callable !== null){
            $callable($player, $packet);
        }
        return true;
    }
}
