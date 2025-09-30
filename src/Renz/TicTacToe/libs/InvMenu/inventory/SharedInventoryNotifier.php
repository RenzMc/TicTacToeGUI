<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\inventory;

use pocketmine\inventory\Inventory;

interface SharedInventoryNotifier{

	public function getInventory() : Inventory;
}