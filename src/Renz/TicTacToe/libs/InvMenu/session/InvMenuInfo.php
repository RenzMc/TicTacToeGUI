<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\session;

use Renz\TicTacToe\libs\InvMenu\InvMenu;
use Renz\TicTacToe\libs\InvMenu\type\graphic\InvMenuGraphic;
use pocketmine\inventory\Inventory;

final class InvMenuInfo{

	/** @var InvMenu */
	private $menu;

	/** @var Inventory */
	private $inventory;

	/** @var InvMenuGraphic */
	private $graphic;

	public function __construct(InvMenu $menu, Inventory $inventory, InvMenuGraphic $graphic){
		$this->menu = $menu;
		$this->inventory = $inventory;
		$this->graphic = $graphic;
	}

	public function getMenu() : InvMenu{
		return $this->menu;
	}

	public function getInventory() : Inventory{
		return $this->inventory;
	}

	public function getGraphic() : InvMenuGraphic{
		return $this->graphic;
	}
}