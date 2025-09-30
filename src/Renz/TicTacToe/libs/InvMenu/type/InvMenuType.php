<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\type;

use Renz\TicTacToe\libs\InvMenu\InvMenu;
use Renz\TicTacToe\libs\InvMenu\type\graphic\InvMenuGraphic;
use pocketmine\inventory\Inventory;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

interface InvMenuType{

	public function createGraphic(Inventory $inventory, Player $player, ?string $name) : ?InvMenuGraphic;

	public function createInventory() : Inventory;

	public function getPosition() : Vector3;
}