<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\inventory;

use pocketmine\block\inventory\BlockInventory;
use pocketmine\inventory\SimpleInventory;
use pocketmine\world\Position;

final class InvMenuInventory extends SimpleInventory implements BlockInventory{

	/** @var Position */
	private $holder;

	public function __construct(int $size){
		parent::__construct($size);
		$this->holder = new Position(0, 0, 0, null);
	}

	public function getHolder() : Position{
		return $this->holder;
	}
}