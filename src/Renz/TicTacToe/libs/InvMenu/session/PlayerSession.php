<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\session;

use Closure;
use Renz\TicTacToe\libs\InvMenu\session\network\PlayerNetwork;
use pocketmine\player\Player;

final class PlayerSession{

	/** @var Player */
	private $player;

	/** @var PlayerNetwork */
	private $network;

	/** @var InvMenuInfo|null */
	private $current_menu;

	/** @var Closure|null */
	private $current_menu_callback;

	public function __construct(Player $player, PlayerNetwork $network){
		$this->player = $player;
		$this->network = $network;
	}

	public function getPlayer() : Player{
		return $this->player;
	}

	public function getNetwork() : PlayerNetwork{
		return $this->network;
	}

	public function getCurrentMenu() : ?InvMenuInfo{
		return $this->current_menu;
	}

	public function setCurrentMenu(?InvMenuInfo $menu, ?Closure $callback = null) : void{
		$this->current_menu = $menu;
		$this->current_menu_callback = $callback;
	}

	public function removeCurrentMenu() : bool{
		if($this->current_menu !== null){
			$this->current_menu = null;
			$callback = $this->current_menu_callback;
			$this->current_menu_callback = null;
			if($callback !== null){
				$callback();
			}
			return true;
		}
		return false;
	}

	public function finalize() : void{
		$this->removeCurrentMenu();
		$this->network->dropPending();
	}
}