<?php

declare(strict_types=1);

namespace Renz\TicTacToe\libs\InvMenu\session;

use Closure;
use Renz\TicTacToe\libs\InvMenu\InvMenu;
use Renz\TicTacToe\libs\InvMenu\InvMenuHandler;
use Renz\TicTacToe\libs\InvMenu\session\network\PlayerNetwork;
use Renz\TicTacToe\libs\InvMenu\type\graphic\InvMenuGraphic;
use pocketmine\player\Player;

final class PlayerWindowDispatcher{

	/** @var Player */
	private $player;

	public function __construct(Player $player){
		$this->player = $player;
	}

	public function dispatch(InvMenuGraphic $graphic, ?Closure $callback = null) : void{
		$graphic->send($this->player, $callback);
	}
}