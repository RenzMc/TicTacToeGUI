<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\inventory;

use Closure;
use Renz\TicTacToe\libs\InvMenu\InvMenu;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryListener;
use pocketmine\item\Item;
use pocketmine\player\Player;

final class SharedInvMenuSynchronizer implements InventoryListener{

	/** @var InvMenu */
	private $menu;

	/** @var SharedInventorySynchronizer */
	private $synchronizer;

	/** @var Player[] */
	private $players = [];

	public function __construct(InvMenu $menu){
		$this->menu = $menu;
		$this->synchronizer = new SharedInventorySynchronizer($this);
	}

	public function getMenu() : InvMenu{
		return $this->menu;
	}

	public function getInventory() : Inventory{
		return $this->menu->getInventory();
	}

	public function getPlayers() : array{
		return $this->players;
	}

	public function onContentChange(Inventory $inventory, array $old_contents) : void{
		$this->synchronizer->onContentChange($inventory, $old_contents);
	}

	public function onSlotChange(Inventory $inventory, int $slot, Item $old_item) : void{
		$this->synchronizer->onSlotChange($inventory, $slot, $old_item);
	}

	/**
	 * @param Player $player
	 * @param Closure|null $callback
	 * @return bool
	 */
	public function sendInventory(Player $player, ?Closure $callback = null) : bool{
		$player_id = $player->getId();
		if(isset($this->players[$player_id])){
			return false;
		}

		$this->players[$player_id] = $player;
		$this->menu->getInventory()->getListeners()->add($this);
		$this->menu->send($player, null, $callback);
		return true;
	}

	public function remove(Player $player) : bool{
		$player_id = $player->getId();
		if(!isset($this->players[$player_id])){
			return false;
		}

		$inventory = $this->menu->getInventory();
		$inventory->getListeners()->remove($this);
		unset($this->players[$player_id]);
		if(count($this->players) > 0){
			$inventory->getListeners()->add($this);
		}
		return true;
	}
}